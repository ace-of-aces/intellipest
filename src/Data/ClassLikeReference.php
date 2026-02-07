<?php

declare(strict_types=1);

namespace AceOfAces\IntelliPest\Data;

use AceOfAces\IntelliPest\Enums\ClassLikeType;

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
