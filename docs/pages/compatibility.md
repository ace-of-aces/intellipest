# Compatibility

## LSP Compatibility

Currently, compatibility has only been tested with the popular [Intelephense](https://intelephense.com/) PHP LSP.

The main requirement for an LSP to benefit from IntelliPest is support for the `@param-closure-this` PHPDoc tag [specified by PHPStan](https://phpstan.org/writing-php-code/phpdocs-basics#callables),
which enables type hinting the `$this` variable inside test cases.

> For PHPStorm users, we recommend using the first-party [Pest plugin](https://plugins.jetbrains.com/plugin/14636-pest) by JetBrains.

## Pest Version Compatibility

Exact API compatibility for minor Pest versions has not been thoroughly tested as of right now. This may improve in future releases.

> IntelliPest currently only supports projects using Pest 4.x
