<?php

namespace App\ESPDeviceSensor\Controller;

use App\API\APIErrorMessages;
use App\API\CommonURL;
use App\API\Traits\HomeAppAPIResponseTrait;
use App\ESPDeviceSensor\Entity\Sensor;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\ESPDeviceSensor\Repository\ORM\Sensors\SensorRepositoryInterface;
use App\ESPDeviceSensor\SensorDataServices\SensorReadingUpdate\UpdateBoundaryReadings\UpdateSensorBoundaryReadingsServiceInterface;
use App\ESPDeviceSensor\Voters\SensorVoter;
use App\UserInterface\Exceptions\ReadingTypeBuilderFailureException;
use App\UserInterface\Exceptions\SensorTypeBuilderFailureException;
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
            $sensorBoundaryUpdateData = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return $this->sendBadRequestJsonResponse([APIErrorMessages::FORMAT_NOT_SUPPORTED]);
        }

        if (empty($sensorBoundaryUpdateData['sensorData']) || !is_numeric($sensorBoundaryUpdateData['sensorId'])) {
            return $this->sendBadRequestJsonResponse([APIErrorMessages::MALFORMED_REQUEST_MISSING_DATA]);
        }

        try {
            $sensorObject = $sensorRepository->findOneById($sensorBoundaryUpdateData['sensorId']);
        } catch (ORMException) {
            return $this->sendInternalServerErrorJsonResponse(['device failed']);
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

        foreach ($sensorBoundaryUpdateData['sensorData'] as $updateData) {
            try {
                $updateSensorBoundaryReadingsDTO = $updateSensorBoundaryReadingsService->createUpdateSensorBoundaryReadingDTO($updateData);

                $readingTypeQueryDTOs[] = $updateSensorBoundaryReadingsService->createReadingTypeQueryDTO($updateSensorBoundaryReadingsDTO);
                $updateSensorBoundaryReadingsDTOs[] = $updateSensorBoundaryReadingsDTO;
            } catch (ReadingTypeBuilderFailureException) {
                $updateReadingDTOErrors[] = $updateData['sensorType'] ?? 'no sensor type provided'  . ' is not a valid sensor type';
            }
        }

        if (empty($updateSensorBoundaryReadingsDTOs) || empty($readingTypeQueryDTOs)) {
            return $this->sendBadRequestJsonResponse(['Could not prepare sensor reading data']);
        }

        try {
            $sensorTypeJoinQueryDTO = $updateSensorBoundaryReadingsService->getReadingTypeObjectJoinQueryDTO($sensorObject->getSensorTypeObject()->getSensorType());
        } catch (SensorTypeBuilderFailureException $e) {
            return $this->sendInternalServerErrorJsonResponse([$e->getMessage()]);
        }

        $sensorReadingTypeObjects = $updateSensorBoundaryReadingsService->findSensorAndReadingTypesToUpdateBoundaryReadings(
            $sensorTypeJoinQueryDTO,
            $readingTypeQueryDTOs,
            $sensorObject->getDeviceObject()->getDeviceNameID(),
            $sensorObject->getSensorName(),
        );
        $sensorObject = array_pop($sensorReadingTypeObjects);

        if (!$sensorObject instanceof SensorTypeInterface) {
            return $this->sendBadRequestJsonResponse([APIErrorMessages::FAILED_TO_PREPARE_DATA]);
        }

        $validationErrors = $updateSensorBoundaryReadingsService->processBoundaryReadingDTOs(
            $updateSensorBoundaryReadingsDTOs,
            $sensorReadingTypeObjects,
            $sensorObject->getSensorTypeName()
        );

        if (!empty($validationErrors)) {
            return $this->sendBadRequestJsonResponse($validationErrors);
        }

        if (!empty($updateReadingDTOErrors)) {
            return $this->sendMultiStatusJsonResponse([$updateReadingDTOErrors]);
        }

        return $this->sendSuccessfulUpdateJsonResponse();
    }
}
