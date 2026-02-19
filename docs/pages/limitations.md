# Known Limitations

IntelliPest is currently unable to accurately reflect the dynamic Test class association of the `$this` variable in test cases determined by the `->in(...)` method in configuration call chains.

## Concrete Example

For a `Pest.php` configuration with different TestCase classes for the `Feature` and `Unit` folders, like this:

```php
pest()->extends(Tests\TestCase::class)
    ->extend(Illuminate\Foundation\Testing\RefreshDatabase::class)
    ->in('Feature');

pest()->extend(Tests\UnitTestCase::class)
    ->in('Unit');
```

The resulting type hint in all test cases will **always** be a union of both TestCase classes, regardless of whether the test is located in `Feature` or `Unit`:

![$this type hint screenshot](/screenshot-type-hint.png)

> It's important to be aware of this while writing tests, as suggestions from both TestCase classes will appear on the `$this` variable inside of tests, even though they _might_ not be available (possibly resulting in a runtime exception when called from the wrong context).

## Technical Details

This is due to IntelliPest's helper file approach only being able to override the `@param-closure-this` PHPDoc tag for [Pest's global testing function declarations](https://pestphp.com/docs/writing-tests).
