<?php

namespace App\Controller\Api;

use IWF\JsonRequestCheckBundle\Attribute\JsonRequestCheck;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Example API controller demonstrating the JsonRequestCheck attribute.
 */
class ApiController extends AbstractController
{
    /**
     * Example endpoint that limits the maximum JSON payload size.
     *
     * This endpoint will automatically reject any JSON payload larger than 5KB.
     */
    #[Route(
        path: '/api/data',
        name: 'api_data',
        requirements: ['_format' => 'json'],
        defaults: ['_format' => 'json'],
        methods: [Request::METHOD_POST],
    )]
    #[JsonRequestCheck(maxJsonContentSize: 5120)] // Limited to 5KB
    public function __invoke(Request $request): JsonResponse
    {
        // The request has already been validated for size by the JsonRequestCheck attribute
        $jsonData = json_decode($request->getContent(), true);

        return $this->json([
            'status' => 'success',
            'contentSize' => $request->server->get('HTTP_CONTENT_LENGTH'),
            ...$jsonData
        ]);
    }
}