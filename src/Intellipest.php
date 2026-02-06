<?php

declare(strict_types=1);

namespace AceOfAces\Intellipest;

use AceOfAces\Intellipest\Visitors\PestConfigVisitor;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\ParserFactory;

final class Intellipest
{
    public function __construct(
        public string $configPath = 'tests/Pest.php'
    ) {}

    public function analyze(): PestConfigVisitor
    {
        $code = file_get_contents($this->configPath);
        $parser = (new ParserFactory)->createForHostVersion();

        try {
            $ast = $parser->parse($code);
        } catch (\Error $error) {
            echo "Parse error: {$error->getMessage()}\n";

            return new PestConfigVisitor;
        }

        $visitor = new PestConfigVisitor;
        $traverser = new NodeTraverser;

        $traverser->addVisitor(new NameResolver);
        $traverser->addVisitor($visitor);
        $traverser->traverse($ast);

        $this->printResults($visitor);

        return $visitor;
    }

    private function printResults(PestConfigVisitor $visitor): void
    {
        foreach ($visitor->getPestCalls() as $i => $call) {
            echo "PestCall #$i:\n";
            foreach ($call->classesAndTraits as $ref) {
                echo "  {$ref->type->value}: {$ref->name}\n";
            }
            echo '  in: '.($call->in ?? 'null')."\n";
        }

        foreach ($visitor->getExpectCalls() as $i => $call) {
            echo "ExpectCall #$i:\n";
            echo "  name: {$call->name}\n";
        }

        foreach ($visitor->getUsesCalls() as $i => $call) {
            echo "UsesCall #$i:\n";
            foreach ($call->classesAndTraits as $ref) {
                echo "  {$ref->type->value}: {$ref->name}\n";
            }
            echo '  in: '.($call->in ?? 'null')."\n";
        }
    }
}
