<?php

declare(strict_types=1);

namespace AceOfAces\IntelliPest\Data;

/**
 * Represents a pest()->extend(...)->use(...)->in(...) call chain.
 */
final readonly class PestCall
{
    /**
     * @param  list<ClassLikeReference>  $classesAndTraits
     */
    public function __construct(
        public array $classesAndTraits = [],
        public ?string $in = null,
    ) {}
}
