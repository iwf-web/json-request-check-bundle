<?php

namespace IWF\JsonRequestCheckBundle\Check\Checks;

use IWF\JsonRequestCheckBundle\Check\JsonRequestCheckerInterface;
use IWF\JsonRequestCheckBundle\Check\JsonRequestCheckResult;
use IWF\JsonRequestCheckBundle\Exception\ContentLengthMismatchException;
use IWF\JsonRequestCheckBundle\Exception\PayloadTooLargeException;
use IWF\JsonRequestCheckBundle\Provider\MaxContentLengthValueProvider;
use Symfony\Component\HttpFoundation\Request;

readonly class MaxContentLengthChecker implements JsonRequestCheckerInterface
{
    public function __construct(
        private MaxContentLengthValueProvider $maxContentLengthValueProvider,
    ) {}

    public function check(Request $request): JsonRequestCheckResult
    {
        $declaredContentLength = (int)$request->server->get('HTTP_CONTENT_LENGTH');
        $controllerClassAndAction = $request->attributes->get('_controller');
        $maxContentLength = $this->maxContentLengthValueProvider->getMaxContentLengthValue($controllerClassAndAction);
        $actualContentLength = strlen($request->getContent());

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

        $declaredContentLength = (int)$request->server->get('HTTP_CONTENT_LENGTH');
        $actualContentLength = strlen($request->getContent());

        if ($declaredContentLength === 0 && $actualContentLength === 0) {
            return false;
        }

        $contentTypeFormat = $request->getContentTypeFormat();
        $contentTypeHeader = $request->headers->get('Content-Type', '');

        $isJsonFormat = in_array($contentTypeFormat, ['json', 'txt']);
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