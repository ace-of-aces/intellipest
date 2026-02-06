<?php

declare(strict_types=1);

namespace AceOfAces\IntelliPest\Data;

/**
 * Represents an expect()->extend('name', fn) call chain.
 */
final readonly class ExpectCall
{
    public function __construct(
        public string $name,
    ) {}
}
