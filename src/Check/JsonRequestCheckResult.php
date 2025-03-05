<?php

namespace IWF\JsonRequestCheckBundle\Check;

class JsonRequestCheckResult
{
    private bool $valid;
    private ?string $errorMessage = null;
    private array $errorContext = [];

    private function __construct(bool $valid)
    {
        $this->valid = $valid;
    }

    public static function createValid(): self
    {
        return new self(true);
    }

    public static function createInvalid(string $errorMessage, array $errorContext = []): self
    {
        $result = new self(false);
        $result->errorMessage = $errorMessage;
        $result->errorContext = $errorContext;

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
}