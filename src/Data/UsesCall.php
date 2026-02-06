<?php

declare(strict_types=1);

namespace AceOfAces\Intellipest\Data;

/**
 * Represents a uses(Foo::class, Bar::class)->in('Feature') call chain.
 */
final readonly class UsesCall
{
    /**
     * @param  list<ClassLikeReference>  $classesAndTraits
     */
    public function __construct(
        public array $classesAndTraits = [],
        public ?string $in = null,
    ) {}
}
