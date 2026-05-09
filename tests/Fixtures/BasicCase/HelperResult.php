<?php

namespace {

    /**
     * Runs the given closure after each test in the current file.
     *
     * @param-closure-this \Tests\Fixtures\Stubs\SomeTestCase  $closure
     */
    function afterEach(?Closure $closure = null): \Pest\PendingCalls\AfterEachCall {}

    /**
     * Runs the given closure before each test in the current file.
     *
     * @param-closure-this \Tests\Fixtures\Stubs\SomeTestCase  $closure
     */
    function beforeEach(?Closure $closure = null): \Pest\PendingCalls\BeforeEachCall {}

    /**
     * Adds the given closure as a test. The first argument
     * is the test description; the second argument is
     * a closure that contains the test expectations.
     *
     * @param-closure-this \Tests\Fixtures\Stubs\SomeTestCase  $closure
     *
     * @return ($description is string ? TestCall : HigherOrderTapProxy|TestCall)
     */
    function test(?string $description = null, ?Closure $closure = null): \Pest\Support\HigherOrderTapProxy|\Pest\PendingCalls\TestCall {}

    /**
     * Adds the given closure as a test. The first argument
     * is the test description; the second argument is
     * a closure that contains the test expectations.
     *
     * @param-closure-this \Tests\Fixtures\Stubs\SomeTestCase  $closure
     */
    function it(string $description, ?Closure $closure = null): \Pest\PendingCalls\TestCall {}

}

namespace Pest {

    class Expectation {
        public function toBeOne(): self {}
    }

}

namespace Pest\Expectations {

    class OppositeExpectation {
        public function toBeOne(): self {}
    }

}
