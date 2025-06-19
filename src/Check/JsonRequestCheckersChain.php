<?php

namespace IWF\JsonRequestCheckBundle\Check;

use IWF\JsonRequestCheckBundle\Exception\JsonRequestValidationException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class JsonRequestCheckersChain
{
    /**
     * @var array<JsonRequestCheckerInterface>
     */
    private $checkers; // TODO currently php 7 syntax

    /**
     * @param JsonRequestCheckerInterface[] $checkers
     */
    public function __construct()
    {
        // TODO currently php 7 syntax
        $this->checkers = [];
    }


    /**
     * @param KernelEvent $event
     * @throws HttpExceptionInterface
     * @return void
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
     * (╯°□°)╯︵ ┻━┻
     * @throws HttpExceptionInterface
     */
    private function handleInvalidRequest(KernelEvent $event, Request $request, JsonRequestCheckResult $result): void
    {
        $request->request->replace();

        $event->stopPropagation();

        $exceptionClass = $result->getCustomExceptionClass() ?? JsonRequestValidationException::class;

        throw new $exceptionClass(
            $result->getErrorMessage(),
            $result->getErrorContext()
        );
    }

}
