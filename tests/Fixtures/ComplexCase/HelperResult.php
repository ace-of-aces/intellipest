<?php

namespace {

    /**
     * Runs the given closure after each test in the current file.
     *
     * @param-closure-this \Tests\Fixtures\Stubs\SomeTestCase|\Tests\Fixtures\Stubs\DuskTestCase  $closure
     */
    function afterEach(?Closure $closure = null): \Pest\PendingCalls\AfterEachCall {}

    /**
     * Runs the given closure before each test in the current file.
     *
     * @param-closure-this \Tests\Fixtures\Stubs\SomeTestCase|\Tests\Fixtures\Stubs\DuskTestCase  $closure
     */
    function beforeEach(?Closure $closure = null): \Pest\PendingCalls\BeforeEachCall {}

    /**
     * Adds the given closure as a test. The first argument
     * is the test description; the second argument is
     * a closure that contains the test expectations.
     *
     * @param-closure-this \Tests\Fixtures\Stubs\SomeTestCase|\Tests\Fixtures\Stubs\DuskTestCase  $closure
     *
     * @return ($description is string ? TestCall : HigherOrderTapProxy|TestCall)
     */
    function test(?string $description = null, ?Closure $closure = null): \Pest\Support\HigherOrderTapProxy|\Pest\PendingCalls\TestCall {}

    /**
     * Adds the given closure as a test. The first argument
     * is the test description; the second argument is
     * a closure that contains the test expectations.
     *
     * @param-closure-this \Tests\Fixtures\Stubs\SomeTestCase|\Tests\Fixtures\Stubs\DuskTestCase  $closure
     */
    function it(string $description, ?Closure $closure = null): \Pest\PendingCalls\TestCall {}

}

namespace Pest {

    class Expectation {
        public function toBePositive(): self {}
        public function toBeEmail(): self {}
        public function toHaveLength(int $length = 1): self {}
        public function toEqualFoo(): bool {}
        public function toContainValues(mixed ...$values): self {}
        public function toAppendInto(array &$bucket): array {}
        public function toTransformWith(Closure $transform): mixed {}
        public function toUseThreshold(int|float $threshold, ?string $message = null): int|false {}
        public function toMatchAny(string|int $expected, array $options = []): self {}
    }

}

namespace Pest\Expectations {

    class OppositeExpectation {
        public function toBePositive(): self {}
        public function toBeEmail(): self {}
        public function toHaveLength(int $length = 1): self {}
        public function toEqualFoo(): bool {}
        public function toContainValues(mixed ...$values): self {}
        public function toAppendInto(array &$bucket): array {}
        public function toTransformWith(Closure $transform): mixed {}
        public function toUseThreshold(int|float $threshold, ?string $message = null): int|false {}
        public function toMatchAny(string|int $expected, array $options = []): self {}
    }

}

namespace Tests\Fixtures\Stubs {

    class SomeTestCase
    {
        use \Tests\Fixtures\Stubs\RefreshDatabaseTrait;
    }

}

namespace Tests\Fixtures\Stubs {

    class DuskTestCase
    {
        use \Tests\Fixtures\Stubs\DatabaseMigrationsTrait;
    }

}
