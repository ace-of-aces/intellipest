<?php

declare(strict_types=1);

arch()
    ->expect('AceOfAces\IntelliPest')
    ->toUseStrictTypes()
    ->not->toUse(['die', 'var_dump']);
