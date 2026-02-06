<?php

declare(strict_types=1);

namespace AceOfAces\IntelliPest\Data;

/**
 * Aggregate configuration extracted from a Pest.php file.
 *
 * Holds all the data needed for IDE helper file generation:
 * custom expectations, test case extensions with traits, and
 * default traits not scoped to a specific test case.
 */
final readonly class PestConfig
{
    /**
     * @param  list<string>  $expectations  Custom expectation method names
     * @param  list<TestCaseExtension>  $testCaseExtensions  Test case bindings with traits
     * @param  list<string>  $defaultTestCaseTraits  Traits not associated with a specific test case
     */
    public function __construct(
        public array $expectations = [],
        public array $testCaseExtensions = [],
        public array $defaultTestCaseTraits = [],
    ) {}
}
