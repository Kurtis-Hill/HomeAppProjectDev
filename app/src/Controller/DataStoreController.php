<?php

namespace App\Controller;

use App\Repository\Logs\LogsRepository;
use App\Services\API\CommonURL;
use Elastica\Client;
use Elastica\Query;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class DataStoreController extends AbstractController
{
    #[Route(CommonURL::USER_BASE_API_URL . 'query/user', name: 'get-user-indicies', methods: [Request::METHOD_GET])]
    public function listUserQueryBucket(Request $request, Client $client, LogsRepository $logsRepository): JsonResponse
    {
//        $user = $this->getUser();


        $response = $client->request(
            '_cat/indices?format=json',
            'GET',
        )->getData();
//        dd($response);


        $query = new Query();


        $query->toArray();

//        dd($query);
//        $bool = new BoolQuery();
//        $response = $client->request()->
dd($logsRepository->search(), $response);
//        dd($res);
//        $this->denyAccessUnlessGranted(UserVoter::CAN_GET_USER, $user);

//        $query = $request->query->get('query');
//        $bucket = $request->query->get('bucket');

//        $buckets = $elasticService->getBuckets($query, $bucket);

        return $this->json($buckets);
    }
}
