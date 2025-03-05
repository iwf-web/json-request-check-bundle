<?php

namespace IWF\JsonRequestCheckBundle\Check;

use IWF\JsonRequestCheckBundle\Exception\JsonRequestValidationException;
use Symfony\Component\DependencyInjection\Attribute\AutowireIterator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\KernelEvent;

readonly class JsonRequestCheckersRepository
{
    /**
     * @param iterable<JsonRequestCheckerInterface> $checkers
     */
    public function __construct(
        #[AutowireIterator('iwf.jsonRequestChecker')]
        private iterable $checkers,
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
