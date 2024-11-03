<?php

namespace App\Controller\User\UserControllers;

use App\Services\API\CommonURL;
use Elastica\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class ElasticBucketsController extends AbstractController
{
    #[Route(CommonURL::USER_BASE_API_URL . 'query/user', name: 'get-user-indicies', methods: [Request::METHOD_GET])]
    public function listUserQueryBucket(
        Request $request,
        Client $client
    ): JsonResponse {
        $indicies = $client->getCluster()->getIndexNames();

        return new JsonResponse($indicies);
    }

    public function logSearchQuery(
        Client $client,
    ): JsonResponse {

    }
}
