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

namespace IWFWeb\JsonRequestCheckBundle\Check\Checks;

use IWFWeb\JsonRequestCheckBundle\Check\JsonRequestCheckerInterface;
use IWFWeb\JsonRequestCheckBundle\Check\JsonRequestCheckResult;
use IWFWeb\JsonRequestCheckBundle\Exception\ContentLengthMismatchException;
use IWFWeb\JsonRequestCheckBundle\Exception\PayloadTooLargeException;
use IWFWeb\JsonRequestCheckBundle\Provider\MaxContentLengthValueProvider;
use Symfony\Component\HttpFoundation\Request;

readonly class MaxContentLengthChecker implements JsonRequestCheckerInterface
{
    public function __construct(
        private MaxContentLengthValueProvider $maxContentLengthValueProvider,
    ) {}

    public function check(Request $request): JsonRequestCheckResult
    {
        $declaredContentLength = (int) $request->server->get('HTTP_CONTENT_LENGTH');
        $controllerClassAndAction = $request->attributes->get('_controller');
        $maxContentLength = $this->maxContentLengthValueProvider->getMaxContentLengthValue($controllerClassAndAction);
        $actualContentLength = \strlen($request->getContent());

        if ($actualContentLength !== $declaredContentLength) {
            return JsonRequestCheckResult::createInvalid(customExceptionClass: ContentLengthMismatchException::class);
        }

        if ($actualContentLength > $maxContentLength) {
            return JsonRequestCheckResult::createInvalid(
                null,
                [
                    'receivedLength' => $actualContentLength,
                    'allowedLength' => $maxContentLength,
                ],
                PayloadTooLargeException::class,
            );
        }

        return JsonRequestCheckResult::createValid();
    }

    public function supports(Request $request): bool
    {
        if ($request->getMethod() !== Request::METHOD_POST) {
            return false;
        }

        $declaredContentLength = (int) $request->server->get('HTTP_CONTENT_LENGTH');
        $actualContentLength = \strlen($request->getContent());

        if ($declaredContentLength === 0 && $actualContentLength === 0) {
            return false;
        }

        $contentTypeFormat = $request->getContentTypeFormat();
        $contentTypeHeader = $request->headers->get('Content-Type', '');

        $isJsonFormat = \in_array($contentTypeFormat, ['json', 'txt'], true);
        $hasJsonInContentType = str_contains($contentTypeHeader, 'json');

        if (!$isJsonFormat && !$hasJsonInContentType) {
            return false;
        }

        if ($contentTypeFormat === 'txt') {
            return $this->contentLooksLikeJson($request->getContent());
        }

        return true;
    }

    private function contentLooksLikeJson(string $content): bool
    {
        if (empty($content)) {
            return false;
        }

        return str_starts_with($content, '{') || str_starts_with($content, '[');
    }
}
