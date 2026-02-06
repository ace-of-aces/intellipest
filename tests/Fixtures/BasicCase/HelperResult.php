<?php

namespace {

    /**
     * Runs the given closure before all tests in the current file.
     *
     * @param-closure-this \Tests\Fixtures\Stubs\SomeTestCase  $closure
     */
    function beforeAll(Closure $closure): void {}

    /**
     * Runs the given closure before each test in the current file.
     *
     * @param-closure-this \Tests\Fixtures\Stubs\SomeTestCase  $closure
     *
     * @return \Pest\Support\HigherOrderTapProxy<\Pest\Concerns\Expectable|\Pest\PendingCalls\TestCall|\Tests\Fixtures\Stubs\SomeTestCase|mixed
     */
    function beforeEach(?Closure $closure = null): \Pest\PendingCalls\BeforeEachCall {}

    /**
     * Adds the given closure as a test. The first argument
     * is the test description; the second argument is
     * a closure that contains the test expectations.
     *
     * @param-closure-this \Tests\Fixtures\Stubs\SomeTestCase  $closure
     *
     * @return \Pest\Concerns\Expectable|\Pest\PendingCalls\TestCall|\Tests\Fixtures\Stubs\SomeTestCase|mixed
     */
    function test(?string $description = null, ?Closure $closure = null): \Pest\Support\HigherOrderTapProxy|\Pest\PendingCalls\TestCall {}

    /**
     * Adds the given closure as a test. The first argument
     * is the test description; the second argument is
     * a closure that contains the test expectations.
     *
     * @param-closure-this \Tests\Fixtures\Stubs\SomeTestCase  $closure
     *
     * @return \Pest\Concerns\Expectable|\Pest\PendingCalls\TestCall|\Tests\Fixtures\Stubs\SomeTestCase|mixed
     */
    function it(string $description, ?Closure $closure = null): \Pest\PendingCalls\TestCall {}

}

namespace Pest {

    /**
     * @method self toBeOne()
     */
    class Expectation {}

}

namespace Pest\Expectations {

    /**
     * @method self toBeOne()
     */
    class OppositeExpectation {}

}
