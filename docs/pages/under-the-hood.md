# Under The Hood

IntelliPest leverages the [nikic/PHP-Parser](https://github.com/nikic/PHP-Parser) package in order to parse your `Pest.php` as an AST (Abstract Syntax Tree).

This enables it to extract call chains to Pest's configuration API (namely `pest()`, `uses()`, `expect()`).

Based on these function calls and the arguments you pass to them (mainly TestCase classes and Traits), IntelliPest maps those to an internal data structure.

As a final step, IntelliPest takes all of this analyzed data and generates a PHP helper file with the help of templates.
