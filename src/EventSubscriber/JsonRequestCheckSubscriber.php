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

use IWF\JsonRequestCheckBundle\Exception\PayloadTooLargeException;
use IWF\JsonRequestCheckBundle\Provider\JsonRequestCheckMaxContentLengthValueProvider;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\KernelEvents;

readonly class JsonRequestCheckSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private JsonRequestCheckMaxContentLengthValueProvider $maxContentLengthValueProvider,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::CONTROLLER => 'check',
        ];
    }

    public function check(KernelEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        if (!$this->shouldCheckRequest($request)) {
            return;
        }

        $contentLength = (int)$request->server->get('HTTP_CONTENT_LENGTH');
        $controllerClassAndAction = $request->attributes->get('_controller');
        $maxContentLength = $this->maxContentLengthValueProvider->getMaxContentLengthValue($controllerClassAndAction);

        if ($contentLength > $maxContentLength) {
            $this->handleOversizedRequest($event, $request, $contentLength, $maxContentLength);
        }
    }

    /**
     * Determines if the current request should be checked for size limits
     */
    private function shouldCheckRequest(Request $request): bool
    {
        // Only check POST requests
        if ($request->getMethod() !== Request::METHOD_POST) {
            return false;
        }

        // Check for zero content length
        if ((int)$request->server->get('HTTP_CONTENT_LENGTH') === 0) {
            return false;
        }

        // Check if content type is JSON or potentially JSON
        $contentTypeFormat = $request->getContentTypeFormat();
        $contentTypeHeader = $request->headers->get('Content-Type', '');

        $isJsonFormat = in_array($contentTypeFormat, ['json', 'txt']);
        $hasJsonInContentType = str_contains($contentTypeHeader, 'json');

        if (!$isJsonFormat && !$hasJsonInContentType) {
            return false;
        }

        // For 'text/plain', verify if the content actually looks like JSON
        if ($contentTypeFormat === 'txt') {
            return $this->contentLooksLikeJson($request->getContent());
        }

        return true;
    }

    /**
     * Checks if the content starts with { or [ which indicates JSON format
     */
    private function contentLooksLikeJson(string $content): bool
    {
        if (empty($content)) {
            return false;
        }

        return str_starts_with($content, '{') || str_starts_with($content, '[');
    }

    /**
     * Handle requests that exceed the maximum allowed content length
     * (╯°□°)╯︵ ┻━┻
     */
    private function handleOversizedRequest(
        KernelEvent $event,
        Request $request,
        int $contentLength,
        int $maxContentLength
    ): void {
        // Clear request data to prevent processing
        $request->request->replace();

        // Stop event propagation to prevent further processing
        $event->stopPropagation();

        // Throw appropriate exception with details
        throw new PayloadTooLargeException($contentLength, $maxContentLength);
    }
}