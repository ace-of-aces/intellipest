<?php

arch()
    ->expect('AceOfAces\IntelliPest')
    ->toUseStrictTypes()
    ->not->toUse(['die', 'var_dump']);
