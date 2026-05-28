<?php

declare(strict_types=1);

namespace App\Controller\Sensor\OutOfBoundsControllers;

use App\DTOs\Sensor\Request\OutOfBounds\GetOutOfBoundsReadingsRequestDTO;
use App\Entity\User\User;
use App\Services\API\APIErrorMessages;
use App\Services\API\CommonURL;
use App\Services\Sensor\OutOfBounds\Elastic\OutOfBoundsElasticSearchService;
use App\Traits\HomeAppAPITrait;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'out-of-bounds/readings', name: 'out_of_bounds_readings')]
class GetOutOfBoundsReadingsController extends AbstractController
{
    use HomeAppAPITrait;

    public function __construct(private readonly LoggerInterface $elasticLogger)
    {
    }

    /**
     * Get out-of-bounds sensor readings from Elasticsearch.
     *
     * Supports filtering by:
     *  - readingTypes[]: one or more of temperature, humidity, analog, latitude
     *  - threshold + direction (above|below): readings beyond that value by the given amount
     *  - startDate / endDate: ISO 8601 date strings for a time-window query
     *  - sensorReadingID: fetch records for a specific base reading type ID
     *  - limit / offset: pagination (default limit 500, max 1000)
     */
    #[Route('', name: 'get_out_of_bounds_readings', methods: [Request::METHOD_GET])]
    public function getReadings(
        OutOfBoundsElasticSearchService $searchService,
        #[MapQueryString]
        ?GetOutOfBoundsReadingsRequestDTO $requestDTO = null,
    ): JsonResponse {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        }

        $requestDTO ??= new GetOutOfBoundsReadingsRequestDTO();

        try {
            $readings = $searchService->search($requestDTO);
        } catch (\Throwable $e) {
            $this->elasticLogger->error(
                sprintf('Failed to query out-of-bounds readings: %s', $e->getMessage()),
                ['user' => $user->getUserIdentifier()],
            );

            return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_PROCESS_REQUEST]);
        }

        try {
            $normalizedResponse = $this->normalize($readings);
        } catch (ExceptionInterface $e) {
            $this->elasticLogger->error(
                sprintf('Failed to prepare out-of-bounds response: %s', $e->getMessage()),
                ['user' => $user->getUserIdentifier()],
            );

            return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_PREPARE_DATA]);
        }

        return $this->sendSuccessfulJsonResponse($normalizedResponse);
    }
}
