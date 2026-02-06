<?php

declare(strict_types=1);

namespace AceOfAces\IntelliPest;

use AceOfAces\IntelliPest\Data\ClassLikeType;
use AceOfAces\IntelliPest\Data\PestConfig;
use AceOfAces\IntelliPest\Data\TestCaseExtension;
use AceOfAces\IntelliPest\Visitors\PestConfigVisitor;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\ParserFactory;

final class IntelliPest
{
    public function __construct(
        public string $configPath = 'tests/Pest.php',
        public bool $generateMixinExpectations = true
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

        return $visitor;
    }

    /**
     * Analyze the Pest config file and generate the IDE helper file content.
     */
    public function generate(): string
    {
        $visitor = $this->analyze();
        $config = $this->buildConfig($visitor);

        return (new PestHelperGenerator($this->generateMixinExpectations))->generate($config);
    }

    /**
     * Transform the raw visitor data into a PestConfig DTO.
     *
     * Processes PestCall and UsesCall items by separating classes from traits.
     * When a class is encountered, a new TestCaseExtension is started. Traits
     * accumulate into the current extension. Traits without an associated class
     * become defaultTestCaseTraits.
     */
    public function buildConfig(PestConfigVisitor $visitor): PestConfig
    {
        $expectations = array_map(
            fn ($call) => $call->name,
            $visitor->getExpectCalls(),
        );

        $testCaseExtensions = [];
        $defaultTestCaseTraits = [];

        // Process both pest() and uses() calls identically
        $allCalls = array_merge($visitor->getPestCalls(), $visitor->getUsesCalls());

        foreach ($allCalls as $call) {
            $pendingTestCase = null;
            $pendingTraits = [];

            foreach ($call->classesAndTraits as $ref) {
                if ($ref->type === ClassLikeType::Class_) {
                    // Finalize any previous pending extension
                    if ($pendingTestCase !== null) {
                        $testCaseExtensions[] = new TestCaseExtension(
                            testCase: $pendingTestCase,
                            traits: $pendingTraits,
                            directory: $call->in,
                        );
                        $pendingTraits = [];
                    }
                    $pendingTestCase = $ref->name;
                } elseif ($ref->type === ClassLikeType::Trait_) {
                    $pendingTraits[] = $ref->name;
                }
            }

            // Finalize the last pending extension for this call
            if ($pendingTestCase !== null) {
                $testCaseExtensions[] = new TestCaseExtension(
                    testCase: $pendingTestCase,
                    traits: $pendingTraits,
                    directory: $call->in,
                );
            } elseif (count($pendingTraits) > 0) {
                // Traits without an associated class become default traits
                $defaultTestCaseTraits = array_merge($defaultTestCaseTraits, $pendingTraits);
            }
        }

        return new PestConfig(
            expectations: $expectations,
            testCaseExtensions: $testCaseExtensions,
            defaultTestCaseTraits: $defaultTestCaseTraits,
        );
    }
}
