<?php

declare(strict_types=1);

namespace AceOfAces\IntelliPest\Data;

/**
 * Represents an expect()->extend('name', fn) call chain.
 */
final readonly class ExpectCall
{
    /**
     * @param  list<string>  $parameters
     */
    public function __construct(
        public string $name,
        public array $parameters = [],
        public ?string $returnType = null,
    ) {}
}
