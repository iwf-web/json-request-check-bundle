# AGENTS.md

Guidance for AI coding agents in this repo.

## Stack

Symfony bundle (`symfony-bundle` package type). PHP 8.2+, Symfony 6 or 7. No test suite present (`tests/` autoload mapped, no files yet).

## Commands

Dev tools live in `tools/` as a separate Composer install (php-cs-fixer, phpstan + extensions). Use Composer scripts from repo root:

- `composer tools:install` — install dev tools into `tools/vendor`
- `composer lint` — php-cs-fixer check + phpstan (read-only)
- `composer fix` — apply php-cs-fixer fixes
- `composer phpstan` / `composer phpstan:baseline` — static analysis / regen baseline
- `composer php-cs-fixer:check` / `composer php-cs-fixer:fix`

Direct binaries also work: `tools/vendor/bin/phpstan analyse`, `tools/vendor/bin/php-cs-fixer fix`.

PHPStan config: `phpstan.dist.neon` (with `phpstan-baseline.neon`). PHP-CS-Fixer config: `.php-cs-fixer.dist.php` (uses `iwf-web/php-coding-standard`).

## Architecture

Goal: reject oversized JSON request bodies (HashDos protection) before controller runs.

Flow:

1. `EventSubscriber/JsonRequestCheckSubscriber` listens on `KernelEvents::CONTROLLER` and delegates to `Check/JsonRequestCheckersChain`.
2. Chain iterates registered `JsonRequestCheckerInterface` services. Each checker reports `supports()` then returns a `JsonRequestCheckResult`.
3. Invalid result → chain throws (default `JsonRequestValidationException`, or `getCustomExceptionClass()` from result, e.g. `PayloadTooLargeException`).
4. `EventSubscriber/JsonRequestValidationExceptionSubscriber` (priority 10) catches those exceptions and emits a `JsonResponse` (413 for payload-too-large, else exception's status code).

### DI wiring (compiler passes — registered in `src/IWFJsonRequestCheckBundle.php`)

- `JsonRequestCheckersPass`: collects services tagged `iwf.jsonRequestChecker` and calls `JsonRequestCheckersChain::addChecker()` for each. Throws `LogicException` if none found.
- `MaxContentLengthValuePass`: wires `Provider/MaxContentLengthValueProvider` value sources.

Service config: `src/Resources/config/services.yaml`. Bundle extension: `DependencyInjection/IWFJsonRequestCheckExtension`. Config schema: `DependencyInjection/Configuration` → `iwf_json_request_check.default_max_content_length`.

### Per-route limit

Controllers declare overrides via `#[JsonRequestCheck(maxJsonContentSize: 1024)]` (`src/Attribute/JsonRequestCheck.php`). Falls back to `default_max_content_length` config when absent. `MaxContentLengthChecker` reads the resolved value from `MaxContentLengthValueProvider`.

### Adding a new checker

1. Implement `Check/JsonRequestCheckerInterface` (`supports(Request)`, `check(Request): JsonRequestCheckResult`).
2. Register service and tag with `iwf.jsonRequestChecker` (or annotate the class with `#[JsonRequestChecker(priority: …)]` — class-level attribute in `src/Attribute/JsonRequestChecker.php`).
3. Optionally set custom exception class on the result for tailored HTTP status.

## Release

`release-please` driven (`.release-please-manifest.json`, `release-please-config.json`). Conventional Commits required.

## Known quirks

- `JsonRequestCheckersChain::$checkers` lacks a typed property declaration (commented `TODO currently php 7 syntax`). Don't "modernize" without a reason.
- `JsonRequestChecker::getPriority()` returns `string` but type is `int` — existing bug, leave unless fixing intentionally.
