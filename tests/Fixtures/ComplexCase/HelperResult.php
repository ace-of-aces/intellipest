<?php

namespace {

    use Pest\Concerns\Expectable;
    use Pest\PendingCalls\BeforeEachCall;
    use Pest\PendingCalls\TestCall;
    use Pest\Support\HigherOrderTapProxy;

    /**
     * Runs the given closure before all tests in the current file.
     *
     * @param-closure-this \Tests\Fixtures\Stubs\SomeTestCase|\Tests\Fixtures\Stubs\DuskTestCase  $closure
     */
    function beforeAll(Closure $closure): void {}

    /**
     * Runs the given closure before each test in the current file.
     *
     * @param-closure-this \Tests\Fixtures\Stubs\SomeTestCase|\Tests\Fixtures\Stubs\DuskTestCase  $closure
     *
     * @return HigherOrderTapProxy<Expectable|TestCall|\Tests\Fixtures\Stubs\SomeTestCase|\Tests\Fixtures\Stubs\DuskTestCase>|Expectable|TestCall|\Tests\Fixtures\Stubs\SomeTestCase|\Tests\Fixtures\Stubs\DuskTestCase|mixed
     */
    function beforeEach(?Closure $closure = null): BeforeEachCall {}

    /**
     * Adds the given closure as a test. The first argument
     * is the test description; the second argument is
     * a closure that contains the test expectations.
     *
     * @param-closure-this \Tests\Fixtures\Stubs\SomeTestCase|\Tests\Fixtures\Stubs\DuskTestCase  $closure
     *
     * @return Expectable|TestCall|\Tests\Fixtures\Stubs\SomeTestCase|\Tests\Fixtures\Stubs\DuskTestCase|mixed
     */
    function test(?string $description = null, ?Closure $closure = null): HigherOrderTapProxy|TestCall {}

    /**
     * Adds the given closure as a test. The first argument
     * is the test description; the second argument is
     * a closure that contains the test expectations.
     *
     * @param-closure-this \Tests\Fixtures\Stubs\SomeTestCase|\Tests\Fixtures\Stubs\DuskTestCase  $closure
     *
     * @return Expectable|TestCall|\Tests\Fixtures\Stubs\SomeTestCase|\Tests\Fixtures\Stubs\DuskTestCase|mixed
     */
    function it(string $description, ?Closure $closure = null): TestCall {}

}

namespace Pest {

    /**
     * @method self toBePositive()
     * @method self toBeEmail()
     */
    class Expectation {}

}

namespace Pest\Expectations {

    /**
     * @method self toBePositive()
     * @method self toBeEmail()
     */
    class OppositeExpectation {}

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
