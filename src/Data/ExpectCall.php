<?php

declare(strict_types=1);

namespace AceOfAces\Intellipest\Data;

/**
 * Represents an expect()->extend('name', fn) call chain.
 */
final readonly class ExpectCall
{
    public function __construct(
        public string $name,
    ) {}
}
