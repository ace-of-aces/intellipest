<?php

declare(strict_types=1);

namespace AceOfAces\IntelliPest\Visitors;

use AceOfAces\IntelliPest\Data\ClassLikeReference;
use AceOfAces\IntelliPest\Data\ExpectCall;
use AceOfAces\IntelliPest\Data\PestCall;
use AceOfAces\IntelliPest\Data\UsesCall;
use AceOfAces\IntelliPest\Support\ClassLikeResolver;
use PhpParser\Node;
use PhpParser\Node\Arg;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Identifier;
use PhpParser\Node\Name;
use PhpParser\Node\Scalar\String_;
use PhpParser\NodeVisitorAbstract;

/**
 * Visits a Pest config file AST and extracts pest(), expect(), and uses() call chains
 * into their respective Data Objects.
 *
 * All extension methods (extend, extends, use, uses) are treated identically,
 * matching Pest's Configuration class where they are all aliases of each other.
 * Each class-like argument is resolved via ClassLikeResolver to determine
 * whether it's a class or a trait.
 */
final class PestConfigVisitor extends NodeVisitorAbstract
{
    /** @var list<PestCall> */
    private array $pestCalls = [];

    /** @var list<ExpectCall> */
    private array $expectCalls = [];

    /** @var list<UsesCall> */
    private array $usesCalls = [];

    public function __construct(
        private readonly ClassLikeResolver $resolver = new ClassLikeResolver,
    ) {}

    public function leaveNode(Node $node): null
    {
        if (! $node instanceof Node\Stmt\Expression) {
            return null;
        }

        $expr = $node->expr;

        // The expression must be a MethodCall or FuncCall to be a call chain we care about.
        if (! $expr instanceof MethodCall && ! $expr instanceof FuncCall) {
            return null;
        }

        // Unwind the chain: walk from outermost MethodCall down to the root FuncCall.
        $chain = $this->unwindChain($expr);

        if ($chain === null) {
            return null;
        }

        [$rootName, $rootArgs, $methods] = $chain;

        match ($rootName) {
            'pest' => $this->processPestChain($methods),
            'expect' => $this->processExpectChain($methods),
            'uses' => $this->processUsesChain($rootArgs, $methods),
            default => null,
        };

        return null;
    }

    /**
     * Unwinds a method call chain from outermost to the root FuncCall.
     *
     * Given: pest()->extend(X)->use(Y)->in('Feature')
     * AST:   MethodCall(MethodCall(MethodCall(FuncCall('pest'), 'extend', [X]), 'use', [Y]), 'in', ['Feature'])
     *
     * Returns: ['pest', [], [{name: 'extend', args: [X]}, {name: 'use', args: [Y]}, {name: 'in', args: ['Feature']}]]
     *
     * @return array{string, Arg[], list<array{name: string, args: Arg[]}>}|null
     */
    private function unwindChain(Node\Expr $expr): ?array
    {
        /** @var list<array{name: string, args: Arg[]}> $methods */
        $methods = [];
        $current = $expr;

        while ($current instanceof MethodCall) {
            $methodName = $current->name;

            // Dynamic method names are not supported.
            if (! $methodName instanceof Identifier) {
                return null;
            }

            // Prepend: we're walking from outermost to innermost, so we reverse order.
            array_unshift($methods, [
                'name' => $methodName->name,
                'args' => $current->args,
            ]);

            $current = $current->var;
        }

        // The innermost node must be a FuncCall with a simple Name.
        if (! $current instanceof FuncCall) {
            return null;
        }

        if (! $current->name instanceof Name) {
            return null;
        }

        $rootName = $current->name->toString();

        if (! in_array($rootName, ['pest', 'expect', 'uses'], true)) {
            return null;
        }

        return [$rootName, $current->args, $methods];
    }

    /**
     * Process a pest() call chain.
     *
     * All extension methods (extend, extends, use, uses) are treated identically
     * since they are all aliases in Pest's Configuration class.
     *
     * @param  list<array{name: string, args: Arg[]}>  $methods
     */
    private function processPestChain(array $methods): void
    {
        $classesAndTraits = [];
        $in = null;

        foreach ($methods as $method) {
            match ($method['name']) {
                'extend', 'extends', 'use', 'uses' => $classesAndTraits = array_merge(
                    $classesAndTraits,
                    $this->resolveClassArgs($method['args']),
                ),
                'in' => $in = $this->extractStringArg($method['args']),
                default => null,
            };
        }

        $this->pestCalls[] = new PestCall(
            classesAndTraits: $classesAndTraits,
            in: $in,
        );
    }

    /**
     * Process an expect() call chain.
     *
     * Recognized methods: extend
     *
     * @param  list<array{name: string, args: Arg[]}>  $methods
     */
    private function processExpectChain(array $methods): void
    {
        foreach ($methods as $method) {
            if ($method['name'] === 'extend') {
                $name = $this->extractStringArg($method['args']);
                if ($name !== null) {
                    $this->expectCalls[] = new ExpectCall(name: $name);
                }
            }
        }
    }

    /**
     * Process a legacy uses() call chain.
     *
     * The root uses() call contains class-like arguments directly.
     * Additional chained extension methods (extend, use, etc.) are also collected.
     *
     * @param  list<Arg>  $rootArgs
     * @param  list<array{name: string, args: Arg[]}>  $methods
     */
    private function processUsesChain(array $rootArgs, array $methods): void
    {
        $classesAndTraits = $this->resolveClassArgs($rootArgs);
        $in = null;

        foreach ($methods as $method) {
            match ($method['name']) {
                'extend', 'extends', 'use', 'uses' => $classesAndTraits = array_merge(
                    $classesAndTraits,
                    $this->resolveClassArgs($method['args']),
                ),
                'in' => $in = $this->extractStringArg($method['args']),
                default => null,
            };
        }

        $this->usesCalls[] = new UsesCall(
            classesAndTraits: $classesAndTraits,
            in: $in,
        );
    }

    /**
     * Extract all class-like arguments and resolve each to a ClassLikeReference.
     *
     * @param  list<Arg>  $args
     * @return list<ClassLikeReference>
     */
    private function resolveClassArgs(array $args): array
    {
        $references = [];

        foreach ($args as $arg) {
            $fqcn = $this->resolveClassConstFetch($arg);
            if ($fqcn !== null) {
                $references[] = new ClassLikeReference(
                    name: $fqcn,
                    type: $this->resolver->resolve($fqcn),
                );
            }
        }

        return $references;
    }

    /**
     * Resolve a Foo::class argument to its fully qualified name string.
     */
    private function resolveClassConstFetch(Arg $arg): ?string
    {
        $value = $arg->value;

        if (! $value instanceof ClassConstFetch) {
            return null;
        }

        if (! $value->class instanceof Name) {
            return null;
        }

        if (! $value->name instanceof Identifier || $value->name->name !== 'class') {
            return null;
        }

        return $value->class->toString();
    }

    /**
     * Extract the first string literal argument from an argument list.
     *
     * @param  list<Arg>  $args
     */
    private function extractStringArg(array $args): ?string
    {
        if (count($args) === 0) {
            return null;
        }

        $value = $args[0]->value;

        if (! $value instanceof String_) {
            return null;
        }

        return $value->value;
    }

    /** @return list<PestCall> */
    public function getPestCalls(): array
    {
        return $this->pestCalls;
    }

    /** @return list<ExpectCall> */
    public function getExpectCalls(): array
    {
        return $this->expectCalls;
    }

    /** @return list<UsesCall> */
    public function getUsesCalls(): array
    {
        return $this->usesCalls;
    }
}
