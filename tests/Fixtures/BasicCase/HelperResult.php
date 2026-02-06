<?php

namespace {

    use Pest\Concerns\Expectable;
    use Pest\PendingCalls\BeforeEachCall;
    use Pest\PendingCalls\TestCall;
    use Pest\Support\HigherOrderTapProxy;

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
     * @return HigherOrderTapProxy<Expectable|TestCall|\Tests\Fixtures\Stubs\SomeTestCase>|Expectable|TestCall|\Tests\Fixtures\Stubs\SomeTestCase|mixed
     */
    function beforeEach(?Closure $closure = null): BeforeEachCall {}

    /**
     * Adds the given closure as a test. The first argument
     * is the test description; the second argument is
     * a closure that contains the test expectations.
     *
     * @param-closure-this \Tests\Fixtures\Stubs\SomeTestCase  $closure
     *
     * @return Expectable|TestCall|\Tests\Fixtures\Stubs\SomeTestCase|mixed
     */
    function test(?string $description = null, ?Closure $closure = null): HigherOrderTapProxy|TestCall {}

    /**
     * Adds the given closure as a test. The first argument
     * is the test description; the second argument is
     * a closure that contains the test expectations.
     *
     * @param-closure-this \Tests\Fixtures\Stubs\SomeTestCase  $closure
     *
     * @return Expectable|TestCall|\Tests\Fixtures\Stubs\SomeTestCase|mixed
     */
    function it(string $description, ?Closure $closure = null): TestCall {}

}

namespace Pest {

    /**
     * Pest\Expectation
     *
     * @method self toBeOne()
     */
    class Expectation {}

}

namespace Pest\Expectations {

    /**
     * Pest\Expectation\OppositeExpectation
     *
     * @method self toBeOne()
     */
    class OppositeExpectation {}

}
