<?php

namespace App\Sensors\Controller\SensorControllers;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\Common\Traits\ValidatorProcessorTrait;
use App\Sensors\DTO\Request\UpdateSensorReadingBoundaryRequestDTO;
use App\Sensors\Entity\Sensor;
use App\Sensors\Exceptions\ReadingTypeNotExpectedException;
use App\Sensors\Exceptions\ReadingTypeNotGivenException;
use App\Sensors\Exceptions\SensorReadingTypeObjectNotFoundException;
use App\Sensors\Exceptions\SensorReadingTypeRepositoryFactoryException;
use App\Sensors\Exceptions\SensorReadingUpdateFactoryException;
use App\Sensors\Repository\ORM\Sensors\SensorRepositoryInterface;
use App\Sensors\SensorDataServices\SensorReadingUpdate\UpdateBoundaryReadings\UpdateSensorBoundaryReadingsServiceInterface;
use App\Sensors\Voters\SensorVoter;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'sensor/', name: 'boundary-controller')]
class UpdateSensorBoundaryReadingsController extends AbstractController
{
    use HomeAppAPITrait;
    use ValidatorProcessorTrait;

    #[Route('{id}/boundary-update', name: 'boundary-update', methods: [Request::METHOD_PUT])]
    public function updateSensorReadingBoundary(
        Sensor $sensorObject,
        Request $request,
        UpdateSensorBoundaryReadingsServiceInterface $updateSensorBoundaryReadingsService,
        SensorRepositoryInterface $sensorRepository,
        ValidatorInterface $validator,
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

        $validationErrors = $validator->validate($updateBoundaryReadingRequestDTO);
        if ($this->checkIfErrorsArePresent($validationErrors)) {
            return $this->sendBadRequestJsonResponse($this->getValidationErrorAsArray($validationErrors));
        }

        try {
            $this->denyAccessUnlessGranted(SensorVoter::UPDATE_SENSOR_READING_BOUNDARY, $sensorObject->getDeviceObject());
        } catch (AccessDeniedException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        }

        $sensorProcessingErrors = [];
        $successfulTypes = [];
        foreach ($updateBoundaryReadingRequestDTO->getSensorData() as $updateData) {
            try {
                if (!isset($updateData['readingType'])) {
                    throw new ReadingTypeNotGivenException(ReadingTypeNotGivenException::MESSAGE);
                }
                $readingType = $updateData['readingType'];

                $sensorReadingTypeObject = $updateSensorBoundaryReadingsService->getSensorReadingTypeObject($sensorObject->getSensorNameID(), $readingType);
                if ($sensorReadingTypeObject === null) {
                    throw new SensorReadingTypeObjectNotFoundException(SensorReadingTypeRepositoryFactoryException::READING_TYPE_NOT_FOUND);
                }
                $updateSensorBoundaryBuilder = $updateSensorBoundaryReadingsService->getUpdateBoundaryReadingBuilder($updateData['readingType']);
                $updateSensorBoundaryReadingsDTO = $updateSensorBoundaryBuilder->buildUpdateSensorBoundaryReadingsDTO($updateData, $sensorReadingTypeObject);

                $validationErrors = $updateSensorBoundaryReadingsService->processBoundaryReadingDTOs(
                    $sensorReadingTypeObject,
                    $updateSensorBoundaryReadingsDTO,
                    $sensorObject->getSensorTypeObject()->getSensorType()
                );

                if (!empty($validationErrors)) {
                    $sensorProcessingErrors =  array_merge($sensorProcessingErrors, $validationErrors);
                } else {
                    $successfulTypes[] = $readingType;
                }
            } catch (ReadingTypeNotGivenException) {
                $sensorProcessingErrors[] = sprintf(
                    APIErrorMessages::OBJECT_NOT_FOUND,
                    $updateData['sensorType'] ?? 'sensor type'
                );
            } catch (
                SensorReadingUpdateFactoryException
                | SensorReadingTypeRepositoryFactoryException
                | SensorReadingTypeObjectNotFoundException
                | ReadingTypeNotExpectedException $e
            ) {
                $sensorProcessingErrors[] = $e->getMessage();
            } catch (NonUniqueResultException) {
                $sensorProcessingErrors[] = sprintf(
                    APIErrorMessages::CONTACT_SYSTEM_ADMIN,
                    'None unique result found for sensor reading type query',
                );
            }
        }

        if (empty($successfulTypes) && !empty($sensorProcessingErrors)) {
            return $this->sendBadRequestJsonResponse($sensorProcessingErrors, 'All sensor boundary update requests failed');
        }

        try {
            $sensorRepository->flush();
        } catch (ORMException $e) {
            return $this->sendInternalServerErrorJsonResponse([$e->getMessage()]);
        }

        if (count($successfulTypes) !== count($updateBoundaryReadingRequestDTO->getSensorData())) {
            return $this->sendMultiStatusJsonResponse(
                $sensorProcessingErrors,
                ['successfullyUpdated' => $successfulTypes],
                'Some sensor boundary update requests failed'
            );
        }

        return $this->sendSuccessfulUpdateJsonResponse();
    }
}
