<?php

namespace App\Sensors\Controller\SensorControllers;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\Common\Traits\ValidatorProcessorTrait;
use App\Sensors\DTO\Request\UpdateSensorReadingBoundaryRequestDTO;
use App\Sensors\DTO\Response\ReadingTypes\BoundaryReadingResponse\StandardReadingType\ReadingTypeBoundaryReadingResponseInterface;
use App\Sensors\Entity\Sensor;
use App\Sensors\Exceptions\ReadingTypeNotExpectedException;
use App\Sensors\Exceptions\ReadingTypeNotSupportedException;
use App\Sensors\Exceptions\SensorReadingTypeObjectNotFoundException;
use App\Sensors\Exceptions\SensorReadingTypeRepositoryFactoryException;
use App\Sensors\Exceptions\SensorReadingUpdateFactoryException;
use App\Sensors\Exceptions\SensorUpdateFactoryException;
use App\Sensors\Factories\ReadingTypeFactories\ReadingTypeResponseBuilderFactory;
use App\Sensors\Factories\SensorUpdateFactory\SensorReadingUpdateFactory;
use App\Sensors\Repository\ORM\Sensors\SensorRepositoryInterface;
use App\Sensors\SensorDataServices\SensorReadingUpdate\UpdateBoundaryReadings\UpdateSensorBoundaryReadingsServiceInterface;
use App\Sensors\Voters\SensorVoter;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'sensor/', name: 'boundary-controller')]
class UpdateSensorBoundaryReadingsController extends AbstractController
{
    use HomeAppAPITrait;
    use ValidatorProcessorTrait;

    #[ArrayShape([ReadingTypeBoundaryReadingResponseInterface::class])]
    private array $successfullyProcessedTypes = [];

    #[Route('{id}/boundary-update', name: 'boundary-update', methods: [Request::METHOD_PUT])]
    public function updateSensorReadingBoundary(
        Sensor $sensorObject,
        Request $request,
        ValidatorInterface $validator,
        UpdateSensorBoundaryReadingsServiceInterface $updateSensorBoundaryReadingsService,
        SensorRepositoryInterface $sensorRepository,
        SensorReadingUpdateFactory $sensorUpdateFactory,
        ReadingTypeResponseBuilderFactory $readingTypeResponseBuilderFactory,
    ): Response {
        $updateBoundaryReadingRequestDTO = new UpdateSensorReadingBoundaryRequestDTO();
        try {
            $this->deserializeRequest(
                $request->getContent(),
                UpdateSensorReadingBoundaryRequestDTO::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $updateBoundaryReadingRequestDTO]
            );
        } catch (NotEncodableValueException) {
            return $this->sendBadRequestJsonResponse([APIErrorMessages::FORMAT_NOT_SUPPORTED]);
        }

        $requestDTOValidationErrors = $validator->validate($updateBoundaryReadingRequestDTO);
        if ($this->checkIfErrorsArePresent($requestDTOValidationErrors)) {
            return $this->sendBadRequestJsonResponse($this->getValidationErrorAsArray($requestDTOValidationErrors));
        }

        try {
            $this->denyAccessUnlessGranted(SensorVoter::UPDATE_SENSOR_READING_BOUNDARY, $sensorObject->getDeviceObject());
        } catch (AccessDeniedException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        }

        $sensorProcessingErrors = [];
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
                $sensorProcessingErrors[] = $this->getValidationErrorAsArray($updateDataValidationErrors);
                continue;
            }

            try {
                $sensorReadingTypeObject = $updateSensorBoundaryReadingsService->getSensorReadingTypeObject(
                    $sensorObject->getSensorNameID(),
                    $updateBoundaryDataDTO->getReadingType()
                );
            } catch (SensorReadingTypeRepositoryFactoryException|SensorReadingTypeObjectNotFoundException $exception) {
                $sensorProcessingErrors[] = $exception->getMessage();
                continue;
            } catch (NonUniqueResultException) {
                $sensorProcessingErrors[] = sprintf(
                    APIErrorMessages::CONTACT_SYSTEM_ADMIN,
                    'None unique result found for sensor reading type query',
                );
                continue;
            }

            try {
                $validationError = $updateSensorBoundaryReadingsService->processBoundaryDataDTO(
                    $updateBoundaryDataDTO,
                    $sensorReadingTypeObject,
                    $sensorObject->getSensorTypeObject()->getSensorType(),
                );
            } catch (SensorReadingUpdateFactoryException|ReadingTypeNotExpectedException|ReadingTypeNotSupportedException $exception) {
                $sensorProcessingErrors[] = $exception->getMessage();
                continue;
            }

            if (!empty($validationError)) {
                $validationErrors[] =  $validationError;
            } else {
                $this->successfullyProcessedTypes[] = $readingTypeResponseBuilderFactory
                    ->getStandardReadingTypeResponseBuilder($sensorReadingTypeObject)
                    ->buildReadingTypeBoundaryReadingsResponseDTO($sensorReadingTypeObject);
            }

        }
        $sensorProcessingErrors = array_merge($sensorProcessingErrors, $validationErrors ?? []);

        if (empty($this->successfullyProcessedTypes) && !empty($sensorProcessingErrors)) {
            return $this->sendBadRequestJsonResponse($sensorProcessingErrors, 'All sensor boundary update requests failed');
        }

        try {
            $sensorRepository->flush();
        } catch (ORMException|OptimisticLockException $e) {
            return $this->sendInternalServerErrorJsonResponse([sprintf(APIErrorMessages::QUERY_FAILURE, 'sensor')]);
        }

        try {
            $normalizedResponse = $this->normalizeResponse($this->getSuccessFullyProcessedResponseDTOs());
        } catch (ExceptionInterface) {
            return $this->sendMultiStatusJsonResponse([APIErrorMessages::FAILED_TO_NORMALIZE_RESPONSE]);
        }

        if (count($this->getSuccessFullyProcessedResponseDTOs()) !== count($updateBoundaryReadingRequestDTO->getSensorData())) {
            return $this->sendMultiStatusJsonResponse(
                $sensorProcessingErrors,
                $normalizedResponse,
                'Some sensor boundary update requests failed'
            );
        }

        return $this->sendSuccessfulUpdateJsonResponse($normalizedResponse);
    }

    #[ArrayShape([ReadingTypeBoundaryReadingResponseInterface::class])]
    private function getSuccessFullyProcessedResponseDTOs(): array
    {
        return $this->successfullyProcessedTypes;
    }
}
