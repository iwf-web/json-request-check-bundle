# IWF JSON Request Check Bundle

This Symfony bundle protects against HashDos attacks by limiting the size of JSON requests.

Project

[![License](https://img.shields.io/github/license/iwf-web/json-request-check-bundle?label=License)](LICENSE.txt)
[![Contributor Covenant](https://img.shields.io/badge/Contributor%20Covenant-2.0-4baaaa)][code-of-conduct]
[![Version](https://img.shields.io/packagist/v/iwf-web/json-request-check-bundle?label=latest%20release)][packagist]
[![Version (including pre-releases)](https://img.shields.io/packagist/v/iwf-web/json-request-check-bundle?include_prereleases&label=latest%20pre-release)][packagist]
[![Downloads on Packagist](https://img.shields.io/packagist/dt/iwf-web/json-request-check-bundle)][packagist]
[![Required PHP version](https://img.shields.io/packagist/php-v/iwf-web/json-request-check-bundle)][packagist]

## Getting Started

These instructions will help you install this library in your project and tell you how to use it.

### Prerequisites

- PHP 8.2 or higher
- Symfony 6.0 or higher
- Composer for dependency management

### Installing

#### Step 1: Install Package

```bash
composer require iwf-web/json-request-check-bundle
```

#### Step 2: Register Bundle (Symfony < 5.0)

For Symfony versions before 5.0, you need to manually register the bundle in your `config/bundles.php`:

```php
// config/bundles.php
return [
    // ...
    IWFWeb\JsonRequestCheckBundle\IWFJsonRequestCheckBundle::class => ['all' => true],
];
```

### Configuration

Create a configuration file at `config/packages/iwf_json_request_check.yaml`:

```yaml
iwf_json_request_check:
    default_max_content_length: 10240 # Default: 10KB
```

Alternatively, you can define the default value as an environment variable in your `.env` file:

```dotenv
# .env or .env.local
IWF_JSON_REQUEST_CHECK_DEFAULT_MAX_LENGTH=10240
```

and then use it in your configuration file:

```yaml
# config/packages/iwf_json_request_check.yaml
iwf_json_request_check:
    default_max_content_length: '%env(int:IWF_JSON_REQUEST_CHECK_DEFAULT_MAX_LENGTH)%'
```

To have a clue about size you can find a file with a JSON of **4kb** in the examples:
[example-payload-4kb.json](examples/files/example-payload-4kb.json)

### Usage

#### Add the Attribute to Controller Methods

```php
<?php

namespace App\Controller\Api;

use IWFWeb\JsonRequestCheckBundle\Attribute\JsonRequestCheck;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class ApiController extends AbstractController
{
    #[Route('/api/endpoint', methods: [Request::METHOD_POST])]
    #[JsonRequestCheck(maxJsonContentSize: 1024)] // Limits to 1KB for this route
    public function apiEndpoint(Request $request): object
    {
        // Your code here...
        return $this->json(['status' => 'success']);
    }
}
```

#### How It Works

1. When a JSON request is sent to your controller, the `JsonRequestCheckSubscriber` checks the size of the request.
2. If the size exceeds the value specified in the `JsonRequestCheck` attribute, an HTTP 413 (Payload Too Large) Exception is triggered.
3. If no specific value is provided for the route, the global default value from the configuration is used.

### Error Messages

When a request exceeds the allowed size, an HTTP 413 response is automatically returned with the message "JSON payload too large" along with details about the received size and maximum allowed size.

## Local Development Setup

### Installing Development Tools

Dev tools (php-cs-fixer, phpstan) are Composer-managed in `tools/`:

```bash
composer install
composer install --working-dir=tools
```

### Running Code Quality Checks

#### PHP-CS-Fixer (Code Style)

Check code style violations:
```bash
tools/vendor/bin/php-cs-fixer fix --dry-run --diff
```

Fix code style violations automatically:
```bash
tools/vendor/bin/php-cs-fixer fix
```

#### PHPStan (Static Analysis)

Run PHPStan analysis:
```bash
tools/vendor/bin/phpstan analyse
```

Generate PHPStan baseline for existing issues:
```bash
tools/vendor/bin/phpstan analyse --generate-baseline
```

### Development Workflow

Before committing your changes, ensure all checks pass:

```bash
# Check code style
tools/vendor/bin/php-cs-fixer fix --dry-run --diff

# Run static analysis
tools/vendor/bin/phpstan analyse

# If everything passes, fix code style
tools/vendor/bin/php-cs-fixer fix
```

## Built With

- [PHP](https://www.php.net/) - Programming Language
- [Composer](https://getcomposer.org/) - Dependency Management
- [Symfony](https://symfony.com/) - The PHP framework used

## Contributing

Please read [CONTRIBUTING.md][contributing] for details on our code of conduct and the process for submitting pull requests.

This project uses [Conventional Commits](https://www.conventionalcommits.org/).

## Versioning

We use [SemVer](http://semver.org/) for versioning. For available versions, see the [tags on this repository][gh-tags].

## Authors

### Special thanks for all the people who had helped this project so far

* **Nick Steinwand** - [steinwandnick](https://github.com/steinwandnick)
* **Roland Brand** - [bar9](https://github.com/bar9)

See also the full list of [contributors][gh-contributors] who participated in this project.

### I would like to join this list. How can I help the project?

We're currently looking for contributions for the following:

- [ ] Bug fixes
- [ ] Translations
- [ ] etc...

For more information, please refer to our [CONTRIBUTING.md][contributing] guide.

## License

This project is licensed under the MIT License - see the [LICENSE.txt](LICENSE.txt) file for details.

## Acknowledgments

This project currently uses no third-party libraries or copied code.

[packagist]: https://packagist.org/packages/iwf-web/json-request-check-bundle
[gh-tags]: https://github.com/iwf-web/json-request-check-bundle/tags
[gh-contributors]: https://github.com/iwf-web/json-request-check-bundle/contributors
[contributing]: https://github.com/iwf-web/.github/blob/main/CONTRIBUTING.md
[code-of-conduct]: https://github.com/iwf-web/.github/blob/main/CODE_OF_CONDUCT.md
