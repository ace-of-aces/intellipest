<?php

declare(strict_types=1);

namespace AceOfAces\IntelliPest\Support;

use AceOfAces\IntelliPest\Enums\ClassLikeType;

final class ClassLikeResolver
{
    public function resolve(string $fqcn): ClassLikeType
    {
        if (trait_exists($fqcn)) {
            return ClassLikeType::Trait_;
        }

        if (class_exists($fqcn)) {
            return ClassLikeType::Class_;
        }

        return ClassLikeType::Unknown;
    }
}
