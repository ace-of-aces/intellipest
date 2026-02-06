<?php

declare(strict_types=1);

namespace AceOfAces\IntelliPest\Data;

enum ClassLikeType: string
{
    case Class_ = 'class';
    case Trait_ = 'trait';
    case Unknown = 'unknown';
}
