# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Development Commands

This is a Symfony bundle package managed with Composer. It uses PHIVE for managing PHP development tools:

- **Install dependencies**: `composer install`
- **Install development tools**: `phive install && tools/composer install && tools/composer install -d tools`
- **Check code style**: `tools/php-cs-fixer fix --dry-run --diff`
- **Fix code style**: `tools/php-cs-fixer fix`
- **Static analysis**: `tools/phpstan analyse`
- **Generate PHPStan baseline**: `tools/phpstan analyse --generate-baseline`
- **Testing**: No test suite currently present

### Development Workflow

Before committing changes, run:
```bash
# Check code style violations
tools/php-cs-fixer fix --dry-run --diff

# Run static analysis
tools/phpstan analyse

# Fix code style if checks pass
tools/php-cs-fixer fix
```

## Architecture Overview

This Symfony bundle protects against HashDos attacks by limiting JSON request payload sizes through a configurable checking system.

### Core Components

**Bundle Entry Point**: `src/IWFJsonRequestCheckBundle.php`
- Main bundle class that registers compiler passes for dependency injection setup

**Event Handling**: `src/EventSubscriber/JsonRequestCheckSubscriber.php`
- Subscribes to `KernelEvents::CONTROLLER` to check requests before controller execution
- Delegates validation to the checker chain

**Validation Chain**: `src/Check/JsonRequestCheckersChain.php`
- Chain of responsibility pattern for request validation
- Manages multiple checker implementations
- Handles invalid requests by throwing appropriate exceptions

**Configuration**:
- Default max content length configurable via `iwf_json_request_check.default_max_content_length`
- Per-route limits set using `#[JsonRequestCheck(maxJsonContentSize: 1024)]` attribute

**Attribute System**: `src/Attribute/JsonRequestCheck.php`
- PHP 8 attribute for declarative JSON size limits on controller methods
- Takes `maxJsonContentSize` parameter in bytes

### Key Files

- `src/Check/Checks/MaxContentLengthChecker.php`: Implements the actual content length validation
- `src/Provider/MaxContentLengthValueProvider.php`: Provides max content length values from configuration and attributes
- `src/Exception/`: Custom exceptions for different failure scenarios
- `src/DependencyInjection/`: Symfony configuration and compiler passes

### Usage Pattern

Controllers use the attribute to declare limits:
```php
#[JsonRequestCheck(maxJsonContentSize: 1024)]
public function apiEndpoint(Request $request): Response
```

The bundle automatically validates JSON requests against these limits during the controller event phase.

## Configuration

Example configuration in `config/packages/iwf_json_request_check.yaml`:
```yaml
iwf_json_request_check:
    default_max_content_length: 10240
```

## Prerequisites

- PHP 8.2+
- Symfony 6.0+ or 7.0+