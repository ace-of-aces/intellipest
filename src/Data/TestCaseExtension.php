<?php

declare(strict_types=1);

namespace AceOfAces\Intellipest\Data;

/**
 * Represents a test case class with its associated traits and optional directory scope.
 *
 * This is the intermediate model used by the helper generator, built from
 * the raw PestCall/UsesCall data by separating classes from traits.
 */
final readonly class TestCaseExtension
{
    /**
     * @param  list<string>  $traits  Fully qualified trait names
     */
    public function __construct(
        public string $testCase,
        public array $traits = [],
        public ?string $directory = null,
    ) {}
}
