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

use IWFWeb\JsonRequestCheckBundle\Exception\JsonRequestValidationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class JsonRequestCheckersChain
{
    /**
     * @var array<JsonRequestCheckerInterface>
     */
    private $checkers; // TODO currently php 7 syntax

    public function __construct()
    {
        // TODO currently php 7 syntax
        $this->checkers = [];
    }

    /**
     * @throws HttpExceptionInterface
     */
    public function checkEvent(KernelEvent $event): void
    {
        $request = $event->getRequest();

        foreach ($this->checkers as $checker) {
            if (!$checker->supports($request)) {
                continue;
            }

            $result = $checker->check($request);

            if (!$result->isValid()) {
                $this->handleInvalidRequest($event, $request, $result);
            }
        }
    }

    public function addChecker(JsonRequestCheckerInterface $checker): void
    {
        $this->checkers[] = $checker;
    }

    /**
     * Handle invalid requests
     * (╯°□°)╯︵ ┻━┻.
     *
     * @throws HttpExceptionInterface
     */
    private function handleInvalidRequest(KernelEvent $event, Request $request, JsonRequestCheckResult $result): void
    {
        $request->request->replace();

        $event->stopPropagation();

        $exceptionClass = $result->getCustomExceptionClass() ?? JsonRequestValidationException::class;

        throw new $exceptionClass(
            $result->getErrorMessage(),
            $result->getErrorContext(),
        );
    }
}
