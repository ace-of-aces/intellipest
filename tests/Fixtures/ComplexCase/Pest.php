<?php

use Tests\Fixtures\Stubs\DatabaseMigrationsTrait;
use Tests\Fixtures\Stubs\DuskTestCase;
use Tests\Fixtures\Stubs\RefreshDatabaseTrait;
use Tests\Fixtures\Stubs\SomeTestCase;

pest()->extend(SomeTestCase::class)->use(RefreshDatabaseTrait::class)->in('Feature');

pest()->extend(DuskTestCase::class)->use(DatabaseMigrationsTrait::class)->in('Browser');

expect()->extend('toBePositive', function () {
    return $this->toBeGreaterThan(0);
});

expect()->extend('toBeEmail', function () {
    return $this->toMatch('/^.+@.+\..+$/');
});
