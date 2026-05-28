<?php

namespace App\Controller\Logs;

use App\DTOs\Logs\GetLogsRequestDTO;
use App\Repository\Common\Elastic\LogRepository;
use App\Services\API\CommonURL;
use App\Traits\HomeAppAPITrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'logs')]
class GetLogsController extends AbstractController
{
    use HomeAppAPITrait;

    #[
        Route('', name: 'get-system-logs', methods: [Request::METHOD_GET]),
        IsGranted('ROLE_ADMIN'),
    ]
    public function getLogs(
        #[MapQueryString] GetLogsRequestDTO $requestDTO,
        LogRepository $logRepository,
    ): JsonResponse {
        try {
            $results = $logRepository->searchLogs(
                keyword: $requestDTO->getKeyword(),
                level: $requestDTO->getLevel(),
                startDate: $requestDTO->getStartDate(),
                endDate: $requestDTO->getEndDate(),
                limit: $requestDTO->getLimit(),
                offset: $requestDTO->getOffset(),
            );
        } catch (\Throwable $e) {
            return $this->sendInternalServerErrorJsonResponse(['Failed to query logs: ' . $e->getMessage()]);
        }

        $hits = [];
        foreach ($results->getResults() as $result) {
            $hits[] = $result->getData();
        }

        return $this->sendSuccessfulJsonResponse([
            'total' => $results->getTotalHits(),
            'limit' => $requestDTO->getLimit(),
            'offset' => $requestDTO->getOffset(),
            'hits' => $hits,
        ]);
    }
}
