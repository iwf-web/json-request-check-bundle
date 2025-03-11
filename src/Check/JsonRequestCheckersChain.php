<?php

namespace IWF\JsonRequestCheckBundle\Check;

use IWF\JsonRequestCheckBundle\Exception\JsonRequestValidationException;
use Symfony\Component\HttpKernel\Event\KernelEvent;

class JsonRequestCheckersChain
{
    /**
     * @param iterable<JsonRequestCheckerInterface> $checkers
     */
    public function __construct(
        private array $checkers,
    ) {}

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
     */
    private function handleInvalidRequest(KernelEvent $event, $request, $result): void
    {
        $request->request->replace();

        $event->stopPropagation();

        throw new JsonRequestValidationException(
            $result->getErrorMessage(),
            $result->getErrorContext()
        );
    }

}
