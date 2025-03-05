<?php
/*
 * This file is part of the IWFJsonRequestCheckBundle package.
 *
 * (c) IWF AG / IWF Web Solutions <info@iwf.ch>
 * Author: Nick Steinwand <n.steinwand@iwf.ch>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace IWF\JsonRequestCheckBundle\Provider;

/**
 * Provides the maximum content length for JSON requests based on controller configuration.
 *
 * This provider resolves the appropriate max content length value for a given controller
 * by checking the controller-specific configuration or falling back to the default value.
 */
readonly class MaxContentLengthValueProvider
{
    /**
     * @param array<string, int> $jsonRequestCheckClassMap Map of controller class::method to max content length
     * @param int $defaultMaxContentLength Default max content length used as fallback
     */
    public function __construct(
        private array $jsonRequestCheckClassMap,
        private int $defaultMaxContentLength,
    ) {}

    /**
     * Gets the maximum content length value for a given controller.
     *
     * The lookup process checks for:
     * 1. An exact match with the __invoke method appended (for invokable controllers)
     * 2. An exact match with the provided controller class and action
     * 3. Falls back to the default max content length
     *
     * @param string $controllerClassAndAction Controller class and action in format "Class::method"
     * @return int The maximum allowed content length in bytes
     */
    public function getMaxContentLengthValue(string $controllerClassAndAction): int
    {
        // For invokable controllers, the route may reference the class name only,
        // but the annotation is on the __invoke method
        $invokableControllerKey = $controllerClassAndAction . '::__invoke';

        if (isset($this->jsonRequestCheckClassMap[$invokableControllerKey])) {
            return $this->jsonRequestCheckClassMap[$invokableControllerKey];
        }

        if (isset($this->jsonRequestCheckClassMap[$controllerClassAndAction])) {
            return $this->jsonRequestCheckClassMap[$controllerClassAndAction];
        }

        return $this->defaultMaxContentLength;
    }
}