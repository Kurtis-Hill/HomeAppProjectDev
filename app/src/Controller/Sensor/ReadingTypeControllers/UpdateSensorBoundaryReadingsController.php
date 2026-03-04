<?php

namespace App\Controller\Sensor\ReadingTypeControllers;

use App\DTOs\Sensor\Request\UpdateSensorReadingBoundaryRequestDTO;
use App\DTOs\Sensor\Response\ReadingTypes\BoundaryReadingResponse\BoundaryReadingTypeResponseInterface;
use App\Entity\Sensor\Sensor;
use App\Exceptions\Common\ValidatorProcessorException;
use App\Exceptions\Sensor\ReadingTypeNotExpectedException;
use App\Exceptions\Sensor\ReadingTypeNotSupportedException;
use App\Exceptions\Sensor\SensorReadingTypeObjectNotFoundException;
use App\Exceptions\Sensor\SensorReadingTypeRepositoryFactoryException;
use App\Exceptions\Sensor\SensorReadingUpdateFactoryException;
use App\Exceptions\Sensor\SensorUpdateFactoryException;
use App\Factories\Sensor\ReadingTypeFactories\ReadingTypeResponseBuilderFactory;
use App\Factories\Sensor\SensorUpdateFactory\SensorReadingUpdateFactory;
use App\Repository\Sensor\Sensors\SensorRepositoryInterface;
use App\Services\API\APIErrorMessages;
use App\Services\API\CommonURL;
use App\Services\Request\RequestQueryParameterHandler;
use App\Services\Request\RequestTypeEnum;
use App\Services\Sensor\SensorReadingUpdate\UpdateBoundaryReadings\UpdateSensorBoundaryReadingsHandlerInterface;
use App\Traits\HomeAppAPITrait;
use App\Traits\ValidatorProcessorTrait;
use App\Voters\SensorVoter;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use JetBrains\PhpStorm\ArrayShape;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'sensor/', name: 'boundary-controller')]
class UpdateSensorBoundaryReadingsController extends AbstractController
{
    use HomeAppAPITrait;
    use ValidatorProcessorTrait;

//    public const REQUEST_SUCCESSFUL = 'Request Successful';

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly RequestQueryParameterHandler $requestQueryParameterHandler,
    ) {}

    #[Route('{id}/boundary-update', name: 'boundary-update', methods: [Request::METHOD_PUT])]
    public function updateSensorReadingBoundary(
        #[MapRequestPayload]
        UpdateSensorReadingBoundaryRequestDTO $updateBoundaryReadingRequestDTO,
        Sensor $sensorObject,
        Request $request,
        ValidatorInterface $validator,
        UpdateSensorBoundaryReadingsHandlerInterface $updateSensorBoundaryReadingsService,
        SensorRepositoryInterface $sensorRepository,
        SensorReadingUpdateFactory $sensorUpdateFactory,
        ReadingTypeResponseBuilderFactory $readingTypeResponseBuilderFactory,
    ): JsonResponse {
        try {
            $requestDTO = $this->requestQueryParameterHandler->handlerRequestQueryParameterCreation(
                $request->get(RequestQueryParameterHandler::RESPONSE_TYPE, RequestTypeEnum::FULL->value),
            );
        } catch (ValidatorProcessorException $e) {
            return $this->sendBadRequestJsonResponse($e->getValidatorErrors());
        }

        try {
            $this->denyAccessUnlessGranted(SensorVoter::UPDATE_SENSOR_READING_BOUNDARY, $sensorObject);
        } catch (AccessDeniedException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        }

        /** @var BoundaryReadingTypeResponseInterface[] $successfullyProcessedTypes */
        $successfullyProcessedTypes = [];
        $sensorProcessingErrors = [];
        $validationErrors = [];

        foreach ($updateBoundaryReadingRequestDTO->getSensorData() as $updateData) {
            try {
                $sensorUpdateBuilder = $sensorUpdateFactory->getSensorUpdateBuilder($updateData['readingType'] ?? null);
            } catch (SensorUpdateFactoryException $e) {
                $sensorProcessingErrors[] = $e->getMessage();
                continue;
            }

            $updateBoundaryDataDTO = $sensorUpdateBuilder->buildSensorTypeDTO($updateData);
            $updateDataValidationErrors = $validator->validate($updateBoundaryDataDTO);

            if ($this->checkIfErrorsArePresent($updateDataValidationErrors)) {
                foreach ($this->getValidationErrorAsArray($updateDataValidationErrors) as $error) {
                    $sensorProcessingErrors[] = $error;
                }
                continue;
            }

            try {
                $sensorReadingTypeObject = $updateSensorBoundaryReadingsService->getSensorReadingTypeObject(
                    $sensorObject->getSensorID(),
                    $updateBoundaryDataDTO->getReadingType()
                );
            } catch (SensorReadingTypeRepositoryFactoryException|SensorReadingTypeObjectNotFoundException $exception) {
                $this->logger->error($exception->getMessage(), ['user' => $this->getUser()?->getUserIdentifier()]);
                $sensorProcessingErrors[] = $exception->getMessage();
                continue;
            } catch (NonUniqueResultException) {
                $this->logger->error('Non unique result for sensor reading type object', ['user' => $this->getUser()?->getUserIdentifier()]);
                $sensorProcessingErrors[] = sprintf(APIErrorMessages::CONTACT_SYSTEM_ADMIN, 'None unique result found for sensor reading type');
                continue;
            }

            try {
                $validationError = $updateSensorBoundaryReadingsService->processBoundaryDataDTO(
                    $updateBoundaryDataDTO,
                    $sensorReadingTypeObject,
                    $sensorObject->getSensorTypeObject()::getSensorTypeName(),
                );
            } catch (SensorReadingUpdateFactoryException|ReadingTypeNotExpectedException|ReadingTypeNotSupportedException $exception) {
                $sensorProcessingErrors[] = $exception->getMessage();
                continue;
            }

            if (empty($validationError)) {
                $successfullyProcessedTypes[] = $readingTypeResponseBuilderFactory
                    ->getStandardReadingTypeResponseBuilder($sensorReadingTypeObject)
                    ->buildReadingTypeBoundaryReadingsResponseDTO($sensorReadingTypeObject);
            } else {
                $validationErrors = array_merge($validationErrors, $validationError);
            }
        }

        $processingErrors = array_merge($sensorProcessingErrors, $validationErrors);

        if (empty($successfullyProcessedTypes) && !empty($processingErrors)) {
            return $this->sendBadRequestJsonResponse($processingErrors, 'All sensor boundary update requests failed');
        }

        try {
            $sensorRepository->flush();
        } catch (ORMException|OptimisticLockException) {
            $this->logger->error(sprintf(APIErrorMessages::QUERY_FAILURE, 'sensor'), ['user' => $this->getUser()?->getUserIdentifier()]);
            return $this->sendInternalServerErrorJsonResponse([sprintf(APIErrorMessages::FAILURE, 'sensor')]);
        }

        try {
            $normalizedResponse = $this->normalize($successfullyProcessedTypes, [$requestDTO->getResponseType()]);
        } catch (ExceptionInterface) {
            return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_NORMALIZE_RESPONSE]);
        }

        if (count($successfullyProcessedTypes) !== count($updateBoundaryReadingRequestDTO->getSensorData())) {
            return $this->sendMultiStatusJsonResponse($processingErrors, $normalizedResponse, 'Some sensor boundary update requests failed');
        }

        $this->logger->info('sensor boundary update successful for id:' . $sensorObject->getSensorID(), ['user' => $this->getUser()?->getUserIdentifier()]);

        return $this->sendSuccessfulJsonResponse($normalizedResponse);
    }
}
