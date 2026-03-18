# Copilot Instructions for laravel-slim

## Overview

This is a Laravel package (`revolution/laravel-slim`) that transforms a fresh Laravel 13+ project into either a **console-only** or **stateless API-only** application. It provides two Artisan commands that delete unnecessary files and scaffold the appropriate structure.

## Build, Test & Lint

```bash
# Run all tests
composer test
# or directly:
vendor/bin/phpunit

# Run a single test file
vendor/bin/phpunit tests/Feature/SlimTest.php

# Run a single test method
vendor/bin/phpunit --filter=test_slim_console_successful

# Lint (auto-fixes via Laravel Pint)
composer lint
# or directly:
vendor/bin/pint

# Lint only changed files
vendor/bin/pint --dirty
```

## Architecture

This is a **Laravel package** developed with [Orchestra Testbench](https://packages.tools/testbench), not a standard Laravel application. There is no full app context — use `vendor/bin/testbench` instead of `php artisan` for workbench tasks.

### Package structure

- **`src/SlimServiceProvider.php`** — Registers the two Artisan commands. Auto-discovered via `composer.json` `extra.laravel.providers`.
- **`src/Console/SlimConsoleCommand.php`** — `slim:console` command. Strips a fresh Laravel project down to console-only.
- **`src/Console/SlimApiCommand.php`** — `slim:api` command. Strips a fresh Laravel project to API-only with Sanctum token auth.
- **`src/Console/Concerns/WithCheck.php`** — Trait with 13 validation checks ensuring the target project is pristine before transformation.
- **`src/Console/Concerns/WithDelete.php`** — Trait for safe file/directory deletion with console logging.
- **`src/Console/stubs/api/`** — Stub files copied into the target project during `slim:api` (auth routes, example test).

### How the commands work

Both commands follow the same pattern: **validate → delete → modify → report success**.

1. `WithCheck` trait runs pre-flight validation (routes/web.php exists, no starter kit installed, etc.)
2. Unnecessary files/directories are deleted via `WithDelete` trait
3. `bootstrap/app.php` is surgically modified with `File::replaceInFile()`
4. Stub files are copied/appended for API scaffolding (API command only)

### Testing setup

- Tests use a **skeleton Laravel 13 project** at `tests/skeleton/laravel13/` as a fixture.
- Each test copies the skeleton to a temp directory, runs the command, then asserts file system state.
- Tests verify both success and failure paths for each command.

## Conventions

- **Trait-based composition**: Shared logic lives in `src/Console/Concerns/` traits, not base classes.
- **Stub files**: Template files for generated code go in `src/Console/stubs/`.
- **File manipulation**: Use Laravel's `File` facade (`File::replaceInFile()`, `File::replace()`, `File::copy()`, `File::append()`), not raw PHP file functions.
- **Pint config**: Uses `laravel` preset with `no_unused_imports` disabled. The `tests/skeleton` directory is excluded from linting.
- **PHPUnit only**: No Pest. Tests extend `Tests\TestCase` which extends Testbench's `TestCase`.
- **Strict declarations**: All PHP files use `declare(strict_types=1)`.
