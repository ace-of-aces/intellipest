# Quick Start

![terminal header screenshot](/screenshot-header.png)

## Requirements

Make sure your project meets the following requirements:

- PHP 8.3+
- Pest 4.x
- A [compatible PHP language server](/compatibility#lsp-compatibility)

## Installation

```bash
composer require ace-of-aces/intellipest
```

### Setup

> For non-standard setups (e.g., custom `Pest.php` location), check the [Configuration](/configuration) page first.

For standard project setups, just run this command to generate the helper file:

```bash
./vendor/bin/intellipest
```

The terminal output should look something like this:

![terminal screenshot](/screenshot-console.png)

If the command ran successfully, you should be all set! You may have to restart your LSP for it to register the helper file.
