<?php

namespace App\Controller;

use App\Services\API\CommonURL;
use App\Traits\HomeAppAPITrait;
use Elastica\Client;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class DataStoreController extends AbstractController
{
    use HomeAppAPITrait;

    #[Route(CommonURL::USER_BASE_API_URL . 'query/user', name: 'get-user-indicies', methods: [Request::METHOD_GET])]
    public function listUserQueryBucket(
        Client $client
    ): JsonResponse {
        $indicies = $client->getCluster()->getIndexNames();

        return $this->sendSuccessfulJsonResponse($indicies);
    }

    public function logSearchQuery(
        Client $client,
    ): JsonResponse {

    }
}
