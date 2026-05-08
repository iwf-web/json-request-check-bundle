<?php

/**
 * JSON Request Check Bundle
 *
 * @package   JsonRequestCheckBundle
 * @author    IWF Web Solutions <web-solutions@iwf.ch>
 * @copyright Copyright (c) 2025-2026 IWF Web Solutions <web-solutions@iwf.ch>
 * @license   https://github.com/iwf-web/json-request-check-bundle/blob/main/LICENSE.txt MIT License
 * @link      https://github.com/iwf-web/json-request-check-bundle
 */

namespace IWFWeb\JsonRequestCheckBundle\Check;

class JsonRequestCheckResult
{
    private bool $valid;
    private ?string $errorMessage = null;

    /** @var array<string, mixed> */
    private array $errorContext = [];

    private ?string $customExceptionClass = null;

    private function __construct(bool $valid)
    {
        $this->valid = $valid;
    }

    public static function createValid(): self
    {
        return new self(true);
    }

    /**
     * @param array<string, mixed> $errorContext
     */
    public static function createInvalid(?string $errorMessage = null, array $errorContext = [], ?string $customExceptionClass = null): self
    {
        $result = new self(false);
        $result->errorMessage = $errorMessage;
        $result->errorContext = $errorContext;
        $result->customExceptionClass = $customExceptionClass;

        return $result;
    }

    public function isValid(): bool
    {
        return $this->valid;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    /**
     * @return array<string, mixed>
     */
    public function getErrorContext(): array
    {
        return $this->errorContext;
    }

    public function getCustomExceptionClass(): ?string
    {
        return $this->customExceptionClass;
    }
}
