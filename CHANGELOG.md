# Changelog

All notable changes to `IntelliPest` will be documented in this file.

## v0.4.0 - 2026-04-08

### What's Changed

This version improves the accuracy of the helpers generated for custom expectations.

> [!NOTE]
After upgrading, run `./vendor/bin/intellipest` again for these changes to take effect.

* [Feat]: Improve Custom Expectation Helpers by @ace-of-aces in https://github.com/ace-of-aces/intellipest/pull/7

### New Contributors

* @ace-of-aces made their first contribution in https://github.com/ace-of-aces/intellipest/pull/7

**Full Changelog**: https://github.com/ace-of-aces/intellipest/compare/v0.3.1...v0.4.0

## v0.3.1 - 2026-04-08

### What's Changed

This version adds compatibility for `symfony/console` ^8.0.

* Allow symfony/console ^8.0 alongside ^7.4 by @joschuba in https://github.com/ace-of-aces/intellipest/pull/6
* Bump pnpm/action-setup from 4 to 5 by @dependabot[bot] in https://github.com/ace-of-aces/intellipest/pull/2
* Bump ramsey/composer-install from 3 to 4 by @dependabot[bot] in https://github.com/ace-of-aces/intellipest/pull/3
* Bump actions/deploy-pages from 4 to 5 by @dependabot[bot] in https://github.com/ace-of-aces/intellipest/pull/4
* Bump actions/configure-pages from 5 to 6 by @dependabot[bot] in https://github.com/ace-of-aces/intellipest/pull/5

### New Contributors

* @dependabot[bot] made their first contribution in https://github.com/ace-of-aces/intellipest/pull/2
* @joschuba made their first contribution in https://github.com/ace-of-aces/intellipest/pull/6

**Full Changelog**: https://github.com/ace-of-aces/intellipest/compare/v0.3.0...v0.3.1

## v0.3.0 - 2026-03-13

### What's Changed

* Expectation helper method generation for is now opt-in via the `--expectation-helpers` flag (75bcd7924a9534d4c51f04f122d1410cfb71d4d8)
* Internal refactors and type safety improvements

### Breaking Changes

* IntelliPest no longer generates helper methods for Pest's built-in expectations by default.
  The `--no-expectation-helpers` flag was removed in favor of the explicit `--expectation-helpers` flag. Pass this flag if you need the explicit expectation method stubs.

**Full Changelog**: https://github.com/ace-of-aces/intellipest/compare/v0.2.0...v0.3.0

## v0.2.0 - 2026-02-17

### What's Changed

* Add `afterEach` call to `stubs/global_functions.stub` by @JHWelch in https://github.com/ace-of-aces/intellipest/pull/1
* removed an incorrect stub for the`beforeAll` hook
* improved tests

### New Contributors

* @JHWelch made their first contribution in https://github.com/ace-of-aces/intellipest/pull/1

**Full Changelog**: https://github.com/ace-of-aces/intellipest/compare/v0.1.0...v0.2.0

## v0.1.0 - Initial Release - 2026-02-13

Initial Release of IntelliPest 🚀
