# IWF JSON Request Check Bundle

This Symfony bundle protects against HashDos attacks by limiting the size of JSON requests.

## Installation

### Step 1: Install Package

```bash
composer require iwf-web/json-request-check-bundle
```

### Step 2: Register Bundle (Symfony < 5.0)

For Symfony versions before 5.0, you need to manually register the bundle in your `config/bundles.php`:

```php
// config/bundles.php
return [
    // ...
    IWF\JsonRequestCheckBundle\IWFJsonRequestCheckBundle::class => ['all' => true],
];
```

## Configuration

Create a configuration file at `config/packages/iwf_json_request_check.yaml`:

```yaml
iwf_json_request_check:
    default_max_content_length: 10240 # Default: 10KB
```

Alternatively, you can define the default value as an environment variable in your `.env` file:

```
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

## Usage

### Add the Attribute to Controller Methods

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

### How It Works

1. When a JSON request is sent to your controller, the `JsonRequestCheckSubscriber` checks the size of the request.
2. If the size exceeds the value specified in the `JsonRequestCheck` attribute, an HTTP 413 (Payload Too Large) Exception is triggered.
3. If no specific value is provided for the route, the global default value from the configuration is used.

## Error Messages

When a request exceeds the allowed size, an HTTP 413 response is automatically returned with the message "JSON payload too large" along with details about the received size and maximum allowed size.

## License

This bundle is published under the MIT License. For more information, see the [LICENSE](LICENSE) file.

## Credits

Developed by Nick Steinwand / IWF Web Solutions