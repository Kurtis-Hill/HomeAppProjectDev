<?php

namespace App\ESPDeviceSensor\Controller;

use App\API\APIErrorMessages;
use App\API\CommonURL;
use App\API\Traits\HomeAppAPIResponseTrait;
use App\ESPDeviceSensor\Entity\Sensor;
use App\ESPDeviceSensor\Exceptions\ReadingTypeNotExpectedException;
use App\ESPDeviceSensor\Exceptions\ReadingTypeNotGivenException;
use App\ESPDeviceSensor\Exceptions\SensorReadingTypeObjectNotFoundException;
use App\ESPDeviceSensor\Exceptions\SensorReadingTypeRepositoryFactoryException;
use App\ESPDeviceSensor\Exceptions\SensorReadingUpdateFactoryException;
use App\ESPDeviceSensor\Repository\ORM\Sensors\SensorRepositoryInterface;
use App\ESPDeviceSensor\SensorDataServices\SensorReadingUpdate\UpdateBoundaryReadings\UpdateSensorBoundaryReadingsServiceInterface;
use App\ESPDeviceSensor\Voters\SensorVoter;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;
use JsonException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'sensors/', name: 'boundary-controller')]
class UpdateSensorBoundaryReadingsController extends AbstractController
{
    use HomeAppAPIResponseTrait;

    #[Route('boundary-update', name: 'boundary-update', methods: [Request::METHOD_PUT])]
    public function updateSensorReadingBoundary(
        Request $request,
        UpdateSensorBoundaryReadingsServiceInterface $updateSensorBoundaryReadingsService,
        SensorRepositoryInterface $sensorRepository,
    ): Response {
        try {
            $sensorBoundaryUpdateData = json_decode(
                $request->getContent(),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (JsonException) {
            return $this->sendBadRequestJsonResponse([APIErrorMessages::FORMAT_NOT_SUPPORTED]);
        }

        if (empty($sensorBoundaryUpdateData['sensorData']) || !isset($sensorBoundaryUpdateData['sensorId']) || !is_numeric($sensorBoundaryUpdateData['sensorId'])) {
            return $this->sendBadRequestJsonResponse([APIErrorMessages::MALFORMED_REQUEST_MISSING_DATA]);
        }

        try {
            $sensorObject = $sensorRepository->findOneById($sensorBoundaryUpdateData['sensorId']);
        } catch (ORMException) {
            return $this->sendInternalServerErrorJsonResponse(['Sensor query failed']);
        }

        if (!$sensorObject instanceof Sensor) {
            return $this->sendBadRequestJsonResponse([
                sprintf(
                    APIErrorMessages::OBJECT_NOT_FOUND,
                    'Sensor',
                )
            ]);
        }

        try {
            $this->denyAccessUnlessGranted(SensorVoter::UPDATE_SENSOR_READING_BOUNDARY, $sensorObject->getDeviceObject());
        } catch (AccessDeniedException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        }

        $sensorProcessingErrors = [];
        $successfulTypes = [];
        foreach ($sensorBoundaryUpdateData['sensorData'] as $updateData) {
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
                    $sensorProcessingErrors[$readingType] = $validationErrors;
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
            } catch (NonUniqueResultException $e) {
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

        if (count($successfulTypes) !== count($sensorBoundaryUpdateData['sensorData'])) {
            return $this->sendMultiStatusJsonResponse(
                $sensorProcessingErrors,
                ['Successfully updated sensor boundary readings' => $successfulTypes],
                'Some sensor boundary update requests failed'
            );
        }

        return $this->sendSuccessfulUpdateJsonResponse();
    }
}
