<?php

declare(strict_types=1);

namespace AceOfAces\Intellipest\Data;

/**
 * A resolved class-like reference extracted from a Pest config call chain.
 */
final readonly class ClassLikeReference
{
    public function __construct(
        public string $name,
        public ClassLikeType $type,
    ) {}
}
