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

namespace IWF\JsonRequestCheckBundle\EventSubscriber;

use IWF\JsonRequestCheckBundle\Exception\JsonRequestValidationException;
use IWF\JsonRequestCheckBundle\Exception\PayloadTooLargeException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Exception subscriber that converts PayloadTooLargeException to proper HTTP responses.
 * This subscriber ensures that PayloadTooLargeException instances are properly
 * converted to HTTP 413 responses with a JSON body instead of triggering the
 * default exception handling.
 */
final class JsonRequestValidationExceptionSubscriber implements EventSubscriberInterface
{
    /**
     * High priority to handle these exceptions before other exception handlers.
     */
    private const LISTENER_PRIORITY = 10;

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', self::LISTENER_PRIORITY],
        ];
    }

    /**
     * Handles the kernel exception event for PayloadTooLargeException.
     * If the exception is a PayloadTooLargeException, converts it to a
     * proper JSON response with HTTP status code 413.
     */
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof PayloadTooLargeException) {
            $this->handlePayloadTooLarge($event, $exception);
            return;
        }

        if ($exception instanceof JsonRequestValidationException) {
            $this->handleJsonRequestValidation($event, $exception);
        }
    }

    // TODO combine exception handling
    private function handlePayloadTooLarge(ExceptionEvent $event, PayloadTooLargeException $exception): void
    {
        $responseData = [
            'error' => $exception->getMessage(),
        ];

        if ($exception->getReceivedLength() !== null) {
            $responseData['received_length'] = $exception->getReceivedLength();
        }

        if ($exception->getAllowedLength() !== null) {
            $responseData['allowed_length'] = $exception->getAllowedLength();
        }

        $response = new JsonResponse($responseData, PayloadTooLargeException::HTTP_STATUS_CODE);
        $event->setResponse($response);
    }

    private function handleJsonRequestValidation(ExceptionEvent $event, JsonRequestValidationException $exception): void
    {
        $responseData = [
            'error' => $exception->getMessage(),
        ];

        if (!empty($exception->getErrorContext())) {
            $responseData['context'] = $exception->getErrorContext();
        }

        $response = new JsonResponse($responseData, $exception->getStatusCode());
        $event->setResponse($response);
    }
}