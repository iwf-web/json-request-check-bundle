# IWF JSON Request Check Bundle

This Symfony bundle protects against HashDos attacks by limiting the size of JSON requests.

Project

[![License](https://img.shields.io/github/license/iwf-web/json-request-check-bundle)][license]
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
    IWF\JsonRequestCheckBundle\IWFJsonRequestCheckBundle::class => ['all' => true],
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

use IWF\JsonRequestCheckBundle\Attribute\JsonRequestCheck;
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

## Built With

- [PHP](https://www.php.net/) - Programming Language
- [Composer](https://getcomposer.org/) - Dependency Management
- [Symfony](https://symfony.com/) - The PHP framework used

## Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct, and [CONTRIBUTING.md](CONTRIBUTING.md) for the process for submitting pull requests to us.

## Versioning

We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository][gh-tags].

## Authors

All the authors can be seen in the [AUTHORS.md](AUTHORS.md) file.

Contributors can be seen in the [CONTRIBUTORS.md](CONTRIBUTORS.md) file.

See also the full list of [contributors][gh-contributors] who participated in this project.

## License

This project is licensed under the MIT License - see the [LICENSE.txt](LICENSE.txt) file for details

## Acknowledgments

A list of used libraries and code with their licenses can be seen in the [ACKNOWLEDGMENTS.md](ACKNOWLEDGMENTS.md) file.

[license]: https://github.com/iwf-web/json-request-check-bundle/blob/main/LICENSE.txt
[packagist]: https://packagist.org/packages/iwf-web/json-request-check-bundle
[gh-tags]: https://github.com/iwf-web/json-request-check-bundle/tags
[gh-contributors]: https://github.com/iwf-web/json-request-check-bundle/contributors
