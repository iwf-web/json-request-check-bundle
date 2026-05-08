# Changelog

## [1.1.0](https://github.com/iwf-web/json-request-check-bundle/compare/1.0.0...1.1.0) (2025-06-19)


### ✨ Features

* Refactor to an expandable checker architecture (PR #2: refactor-to-expandable). ([3fa5135](https://github.com/iwf-web/json-request-check-bundle/commit/3fa51357beacb3bd9aa78afb5746205d1e0b0fb1))
* Introduce JsonRequestCheckerInterface + JsonRequestCheckersChain
  (chain-of-responsibility for pluggable checks)
* Extract MaxContentLengthChecker as first chain member
* New JsonRequestCheckersPass compiler pass to register checkers by tag;
  rename JsonRequestCheckPass -> MaxContentLengthValuePass
* Rename JsonRequestCheckMaxContentLengthValueProvider
  -> MaxContentLengthValueProvider
* New JsonRequestCheckResult value object; checkers can throw or return result
* New ContentLengthMismatchException and JsonRequestValidationException base;
  rename PayloadTooLargeExceptionSubscriber
  -> JsonRequestValidationExceptionSubscriber
* Add #[JsonRequestChecker] attribute for tagging custom checkers
* Add 4 KB example payload + README update

## [1.0.0](https://github.com/iwf-web/json-request-check-bundle/compare/b657164...1.0.0) (2025-02-25)


### ✨ Features

* Initial release of the iwf-web/json-request-check-bundle Symfony bundle. ([b657164](https://github.com/iwf-web/json-request-check-bundle/commit/b6571644d4a309761cb135b255d83aafd93cec64))
* Provides HashDos protection by limiting JSON request payload sizes.
* #[JsonRequestCheck(maxJsonContentSize: ...)] attribute for per-route limits
* Configurable default via iwf_json_request_check.default_max_content_length
* KernelEvents::CONTROLLER subscriber validates incoming JSON requests
* PayloadTooLargeException + dedicated exception subscriber
* Compiler pass wires max-content-length value provider
* README, LICENSE, example controller and config
