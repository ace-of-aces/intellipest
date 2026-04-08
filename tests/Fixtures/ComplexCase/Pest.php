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

expect()->extend('toHaveLength', function (int $length = 1) {
    return $this->toHaveCount($length);
});

expect()->extend('toEqualFoo', function (): bool {
    return $this->value === 'foo';
});

expect()->extend('toContainValues', function (mixed ...$values) {
    return $this->toContain(...$values);
});

expect()->extend('toAppendInto', function (array &$bucket): array {
    $bucket[] = $this->value;

    return $bucket;
});

expect()->extend('toTransformWith', fn (Closure $transform): mixed => $transform($this->value));

expect()->extend('toUseThreshold', function (int|float $threshold, ?string $message = null): int|false {
    return $this->value > $threshold ? 1 : false;
});

expect()->extend('toMatchAny', function (string|int $expected, array $options = []) {
    return $this->toEqual($expected);
});
