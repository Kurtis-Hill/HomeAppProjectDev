<?php

namespace App\Controller\Sensor\ReadingTypeControllers;

use App\Builders\Sensor\Response\ReadingTypeResponseBuilders\ReadingTypeResponseBuilder;
use App\Entity\Sensor\ReadingTypes\ReadingTypes;
use App\Exceptions\Common\ValidatorProcessorException;
use App\Repository\Sensor\SensorReadingType\ReadingTypeRepositoryInterface;
use App\Services\API\APIErrorMessages;
use App\Services\API\CommonURL;
use App\Services\Request\PaginationCalculator;
use App\Services\Request\RequestQueryParameterHandler;
use App\Services\Request\RequestTypeEnum;
use App\Traits\HomeAppAPITrait;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'reading-types/', name: 'reading_types')]
class GetReadingTypeController extends AbstractController
{
    private const MAX_READING_TYPE_RETURN_SIZE = 100;

    use HomeAppAPITrait;

    private LoggerInterface $logger;

    private RequestQueryParameterHandler $requestQueryParameterHandler;

    public function __construct(LoggerInterface $elasticLogger, RequestQueryParameterHandler $requestQueryParameterHandler)
    {
        $this->logger = $elasticLogger;
        $this->requestQueryParameterHandler = $requestQueryParameterHandler;
    }

    #[Route('all', name: 'all-reading-types', methods: [Request::METHOD_GET])]
    public function getAllReadingTypes(Request $request, ReadingTypeRepositoryInterface $readingTypeRepository): JsonResponse
    {
        try {
            $requestDTO = $this->requestQueryParameterHandler->handlerRequestQueryParameterCreation(
                $request->get(RequestQueryParameterHandler::RESPONSE_TYPE, RequestTypeEnum::ONLY->value),
                $request->get('page'),
                $request->get('limit', self::MAX_READING_TYPE_RETURN_SIZE),
            );
        } catch (ValidatorProcessorException $e) {
            return $this->sendBadRequestJsonResponse($e->getValidatorErrors());
        }
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

    #[Route('{readingTypeID}', name: 'singular-reading-types', methods: [Request::METHOD_GET])]
    public function getSingleReadingTypes(ReadingTypes $readingType, Request $request): JsonResponse
    {
        try {
            $requestDTO = $this->requestQueryParameterHandler->handlerRequestQueryParameterCreation(
                $request->get(RequestQueryParameterHandler::RESPONSE_TYPE, RequestTypeEnum::ONLY->value),
            );
        } catch (ValidatorProcessorException $e) {
            return $this->sendBadRequestJsonResponse($e->getValidatorErrors());
        }

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
