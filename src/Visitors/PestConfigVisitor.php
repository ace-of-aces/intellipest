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
use PhpParser\Node\Expr\ArrowFunction;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Identifier;
use PhpParser\Node\IntersectionType;
use PhpParser\Node\Name;
use PhpParser\Node\NullableType;
use PhpParser\Node\Param;
use PhpParser\Node\Scalar\String_;
use PhpParser\Node\UnionType;
use PhpParser\Node\VariadicPlaceholder;
use PhpParser\NodeVisitorAbstract;
use PhpParser\PrettyPrinter\Standard as PrettyPrinter;

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
        private readonly PrettyPrinter $prettyPrinter = new PrettyPrinter,
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
     * @return array{string, list<Arg|VariadicPlaceholder>, list<array{name: string, args: list<Arg|VariadicPlaceholder>}>}|null
     */
    private function unwindChain(Node\Expr $expr): ?array
    {
        /** @var list<array{name: string, args: list<Arg|VariadicPlaceholder>}> $methods */
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
                'args' => array_values($current->args),
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

        return [$rootName, array_values($current->args), $methods];
    }

    /**
     * Process a pest() call chain.
     *
     * All extension methods (extend, extends, use, uses) are treated identically
     * since they are all aliases in Pest's Configuration class.
     *
     * @param  list<array{name: string, args: list<Arg|VariadicPlaceholder>}>  $methods
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
     * @param  list<array{name: string, args: list<Arg|VariadicPlaceholder>}>  $methods
     */
    private function processExpectChain(array $methods): void
    {
        foreach ($methods as $method) {
            if ($method['name'] === 'extend') {
                $name = $this->extractStringArg($method['args']);
                if ($name !== null) {
                    [$parameters, $returnType] = $this->extractExpectationSignature($method['args']);

                    $this->expectCalls[] = new ExpectCall(
                        name: $name,
                        parameters: $parameters,
                        returnType: $returnType,
                    );
                }
            }
        }
    }

    /**
     * @param  list<Arg|VariadicPlaceholder>  $args
     * @return array{list<string>, ?string}
     */
    private function extractExpectationSignature(array $args): array
    {
        if (count($args) < 2) {
            return [[], null];
        }

        $callbackArg = $args[1];

        if (! $callbackArg instanceof Arg) {
            return [[], null];
        }

        $callback = $callbackArg->value;

        if (! $callback instanceof Closure && ! $callback instanceof ArrowFunction) {
            return [[], null];
        }

        return [
            array_values(array_map($this->renderParam(...), $callback->params)),
            $this->renderType($callback->returnType),
        ];
    }

    private function renderParam(Param $param): string
    {
        $code = '';

        if ($param->type !== null) {
            $renderedType = $this->renderType($param->type);

            if ($renderedType !== null) {
                $code .= $renderedType.' ';
            }
        }

        if ($param->byRef) {
            $code .= '&';
        }

        if ($param->variadic) {
            $code .= '...';
        }

        if (! $param->var instanceof Variable) {
            return trim($code);
        }

        $variableName = $param->var->name;

        if (! is_string($variableName)) {
            return trim($code);
        }

        $code .= '$'.$variableName;

        if ($param->default !== null) {
            $code .= ' = '.$this->renderDefaultValue($param->default);
        }

        return $code;
    }

    private function renderDefaultValue(Node\Expr $default): string
    {
        if ($default instanceof ConstFetch) {
            $constant = strtolower($default->name->toString());

            if (in_array($constant, ['null', 'true', 'false'], true)) {
                return $constant;
            }
        }

        return $this->prettyPrinter->prettyPrintExpr($default);
    }

    private function renderType(Node|string|null $type): ?string
    {
        if ($type === null) {
            return null;
        }

        if (is_string($type)) {
            return $type;
        }

        if ($type instanceof NullableType) {
            $renderedType = $this->renderType($type->type);

            return $renderedType === null ? null : '?'.$renderedType;
        }

        if ($type instanceof UnionType) {
            return $this->renderTypeList($type->types, '|');
        }

        if ($type instanceof IntersectionType) {
            return $this->renderTypeList($type->types, '&');
        }

        if ($type instanceof Identifier || $type instanceof Name) {
            return $type->toString();
        }

        return null;
    }

    /**
     * @param  array<array-key, Node>  $types
     */
    private function renderTypeList(array $types, string $separator): ?string
    {
        $renderedTypes = [];

        foreach ($types as $type) {
            $renderedType = $this->renderType($type);

            if ($renderedType === null) {
                return null;
            }

            $renderedTypes[] = $renderedType;
        }

        return implode($separator, $renderedTypes);
    }

    /**
     * Process a legacy uses() call chain.
     *
     * The root uses() call contains class-like arguments directly.
     * Additional chained extension methods (extend, use, etc.) are also collected.
     *
     * @param  list<Arg|VariadicPlaceholder>  $rootArgs
     * @param  list<array{name: string, args: list<Arg|VariadicPlaceholder>}>  $methods
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
     * @param  list<Arg|VariadicPlaceholder>  $args
     * @return list<ClassLikeReference>
     */
    private function resolveClassArgs(array $args): array
    {
        $references = [];

        foreach ($args as $arg) {
            if (! $arg instanceof Arg) {
                continue;
            }

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
     * @param  list<Arg|VariadicPlaceholder>  $args
     */
    private function extractStringArg(array $args): ?string
    {
        if (count($args) === 0) {
            return null;
        }

        $firstArg = $args[0];

        if (! $firstArg instanceof Arg) {
            return null;
        }

        $value = $firstArg->value;

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
