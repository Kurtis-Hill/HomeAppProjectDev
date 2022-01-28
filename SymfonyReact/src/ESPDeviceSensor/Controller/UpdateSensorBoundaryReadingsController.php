<?php

namespace App\ESPDeviceSensor\Controller;

use App\API\APIErrorMessages;
use App\API\CommonURL;
use App\API\Traits\HomeAppAPIResponseTrait;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\StandardSensorTypeInterface;
use App\ESPDeviceSensor\SensorDataServices\SensorReadingUpdate\UpdateBoundaryReadings\UpdateSensorBoundaryReadingsServiceInterface;
use App\UserInterface\Exceptions\ReadingTypeBuilderFailureException;
use App\UserInterface\Exceptions\SensorTypeBuilderFailureException;
use JsonException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'sensors', name: 'boundary-controller')]
class UpdateSensorBoundaryReadingsController extends AbstractController
{
    use HomeAppAPIResponseTrait;

    #[Route(CommonURL::USER_HOMEAPP_API_URL . 'sensors/boundary-update', name: 'boundary-update')]
    public function updateSensorReadingBoundary(
        Request $request,
        UpdateSensorBoundaryReadingsServiceInterface $updateSensorBoundaryReadingsService,
    ): Response {
        try {
            $cardData = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return $this->sendBadRequestJsonResponse([APIErrorMessages::FORMAT_NOT_SUPPORTED]);
        }

        foreach ($cardData['sensorData'] as $updateData) {
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
            $sensorTypeJoinQueryDTO = $updateSensorBoundaryReadingsService->getReadingTypeObjectJoinQueryDTO($cardViewObject->getSensorNameID()->getSensorTypeObject()->getSensorType());
        } catch (SensorTypeBuilderFailureException $e) {
            return $this->sendInternalServerErrorJsonResponse([$e->getMessage()]);
        }

        $sensorReadingTypeObjects = $updateSensorBoundaryReadingsService->findSensorAndReadingTypesToUpdateBoundaryReadings(
            $sensorTypeJoinQueryDTO,
            $readingTypeQueryDTOs,
            $cardViewObject->getSensorNameID()
                ->getDeviceObject()
                ->getDeviceNameID(),
            $cardViewObject->getSensorNameID()->getSensorName(),
        );

        $sensorObject = array_pop($sensorReadingTypeObjects);

        if (!$sensorObject instanceof StandardSensorTypeInterface) {
            return $this->sendBadRequestJsonResponse([APIErrorMessages::FAILED_TO_PREPARE_DATA]);
        }
        $validationErrors = $updateSensorBoundaryReadingsService->processBoundaryReadingDTOs($updateSensorBoundaryReadingsDTOs, $sensorReadingTypeObjects, $sensorObject->getSensorTypeName());

        if (!empty($validationErrors)) {
            return $this->sendBadRequestJsonResponse($validationErrors);
        }

        if (!empty($updateReadingDTOErrors)) {
            return $this->sendMultiStatusJsonResponse([$updateReadingDTOErrors]);
        }

        return $this->sendSuccessfulUpdateJsonResponse();
    }
}
