<?php

namespace IWF\JsonRequestCheckBundle\Check\Checks;

use IWF\JsonRequestCheckBundle\Check\JsonRequestCheckerInterface;
use IWF\JsonRequestCheckBundle\Check\JsonRequestCheckResult;
use IWF\JsonRequestCheckBundle\Provider\MaxContentLengthValueProvider;
use Symfony\Component\HttpFoundation\Request;

class MaxContentLengthChecker implements JsonRequestCheckerInterface
{
    public function __construct(
        private readonly MaxContentLengthValueProvider $maxContentLengthValueProvider,
    ) {}

    public function check(Request $request): JsonRequestCheckResult
    {
        $contentLength = (int)$request->server->get('HTTP_CONTENT_LENGTH');
        $controllerClassAndAction = $request->attributes->get('_controller');

        $maxContentLength = $this->maxContentLengthValueProvider->getMaxContentLengthValue($controllerClassAndAction);

        if ($contentLength > $maxContentLength) {
            return JsonRequestCheckResult::createInvalid(
//                TODO implement specific exceptions
                'Payload too large',
                [
                    'received_length' => $contentLength,
                    'allowed_length' => $maxContentLength,
                ]
            );
        }

        return JsonRequestCheckResult::createValid();
    }

    public function supports(Request $request): bool
    {
        if ($request->getMethod() !== 'POST') {
            return false;
        }

        if ((int)$request->server->get('HTTP_CONTENT_LENGTH') === 0) {
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