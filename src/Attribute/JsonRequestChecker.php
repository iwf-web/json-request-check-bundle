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

namespace IWFWeb\JsonRequestCheckBundle\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
readonly class JsonRequestChecker
{
    public function __construct(private int $priority = 0) {}

    public function getPriority(): string
    {
        return $this->priority;
    }
}
