<?php

namespace IWF\JsonRequestCheckBundle\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
readonly class JsonRequestChecker
{
    public function __construct(private int $priority = 0) {}

    public function getPriority(): string
    {
        return $this->priority;
    }
}

