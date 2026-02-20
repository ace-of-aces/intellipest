# Configuration

IntelliPest can be configured through flags to the `intellipest` command.

## `--config` / `-c`

Specify the path to the `Pest.php` configuration file.

**Default:** `tests/Pest.php`

```bash
./vendor/bin/intellipest --config custom/path/Pest.php
```

## `--output` / `-o`

Specify the path to write the generated IDE helper file.

**Default:** `.intellipest/_pest-helper.php`

```bash
./vendor/bin/intellipest --output custom/path/helper.php
```

> You may also set the output directory via the `INTELLIPEST_OUTPUT_DIR` environment variable.

## `--no-expectation-helpers`

Don't generate helper methods for Pest's built-in expectations in the helper file.

```bash
./vendor/bin/intellipest --no-expectation-helpers
```

> Some LSPs like [Intelephense Premium](https://intelephense.com/) support the `@mixin` PHPDoc tag, which is used in Pest's source code, making these helper methods redundant for those users.

## `--shush` / `-s`

Hide the (beautiful) header and footer in the console output.

```bash
./vendor/bin/intellipest --shush
```

## `--watch` / `-w`

Watch the `Pest.php` configuration file and automatically regenerate the helper file when changes are detected. Checks for changes every half second.

```bash
./vendor/bin/intellipest --watch
```

> The watch process continues running until manually stopped (Ctrl+C). Syntax errors in the config file are reported but don't stop the watcher.

## `--quiet` / `-q`

Don't output **any** console messages (useful for CI).

```bash
./vendor/bin/intellipest --quiet
```
