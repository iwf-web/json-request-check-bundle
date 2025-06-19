<?php

namespace IWF\JsonRequestCheckBundle\Check;

class JsonRequestCheckResult
{
    private bool $valid;
    private ?string $errorMessage = null;
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

    public function getErrorContext(): array
    {
        return $this->errorContext;
    }

    public function getCustomExceptionClass(): ?string
    {
        return $this->customExceptionClass;
    }
}