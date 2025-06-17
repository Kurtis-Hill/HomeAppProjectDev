<?php

namespace App\Controller\Sensor\ReadingTypeControllers;

use App\Builders\Sensor\Response\ReadingTypeResponseBuilders\ReadingTypeResponseBuilder;
use App\DTOs\RequestDTO;
use App\Entity\Sensor\ReadingTypes\ReadingTypes;
use App\Repository\Sensor\SensorReadingType\ReadingTypeRepositoryInterface;
use App\Services\API\APIErrorMessages;
use App\Services\API\CommonURL;
use App\Services\Request\PaginationCalculator;
use App\Traits\HomeAppAPITrait;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'reading-types', name: 'reading_types')]
class GetReadingTypeController extends AbstractController
{
    private const MAX_READING_TYPE_RETURN_SIZE = 100;

    use HomeAppAPITrait;

    private LoggerInterface $logger;

    #[Route('', name: 'all-reading-types', methods: [Request::METHOD_GET])]
    public function getAllReadingTypes(
        ReadingTypeRepositoryInterface $readingTypeRepository,
        #[MapQueryString]
        ?RequestDTO $requestDTO = null,
    ): JsonResponse {
        $requestDTO ??= new RequestDTO();

        $allReadingTypes = $readingTypeRepository->findAllPaginatedResults(
            $requestDTO->getLimit(),
            PaginationCalculator::calculateOffset(
                $requestDTO->getLimit(),
                $requestDTO->getPage()
            ),
        );
        foreach ($allReadingTypes as $readingTypeObject) {
            if ($readingTypeObject instanceof ReadingTypes) {
                $readingTypeResponseDTO[] = ReadingTypeResponseBuilder::buildReadingTypeResponseDTO($readingTypeObject);
            }
        }

        if (empty($readingTypeResponseDTO)) {
            return $this->sendInternalServerErrorJsonResponse([sprintf(APIErrorMessages::QUERY_FAILURE, 'Reading types')]);
        }

        try {
            $normalizedReadingTypesDTOs = $this->normalize($readingTypeResponseDTO, [$requestDTO->getResponseType()]);
        } catch (ExceptionInterface $e) {
            $this->logger->error($e->getMessage(), ['user' => $this->getUser()?->getUserIdentifier()]);

            return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_PREPARE_DATA]);
        }

        return $this->sendSuccessfulJsonResponse($normalizedReadingTypesDTOs);
    }

    #[Route('/{readingTypeID}', name: 'singular-reading-types', methods: [Request::METHOD_GET])]
    public function getSingleReadingTypes(
        ReadingTypes $readingType,
        #[MapQueryString]
        ?RequestDTO $requestDTO = null,
    ): JsonResponse {
        $requestDTO ??= new RequestDTO();
        $readingTypeResponseDTO = ReadingTypeResponseBuilder::buildReadingTypeResponseDTO($readingType);

        try {
            $normalizedReadingTypesDTOs = $this->normalize($readingTypeResponseDTO, [$requestDTO->getResponseType()]);
        } catch (ExceptionInterface $e) {
            $this->logger->error($e->getMessage(), ['user' => $this->getUser()?->getUserIdentifier()]);

            return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_PREPARE_DATA]);
        }

        return $this->sendSuccessfulJsonResponse($normalizedReadingTypesDTOs);
    }
}
