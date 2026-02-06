<?php

use Tests\Fixtures\Stubs\RefreshDatabaseTrait;
use Tests\Fixtures\Stubs\SomeTestCase;

uses(SomeTestCase::class, RefreshDatabaseTrait::class)->in('Feature');

expect()->extend('toBeEmail', function () {
    return $this->toMatch('/^.+@.+\..+$/');
});
