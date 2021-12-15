<?php

namespace App\ESPDeviceSensor\Controller;

use App\API\APIErrorMessages;
use App\Devices\Entity\Devices;
use App\ESPDeviceSensor\DTO\Sensor\NewSensorDTO;
use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use App\ESPDeviceSensor\Entity\SensorType;
use App\ESPDeviceSensor\Exceptions\SensorTypeException;
use App\ESPDeviceSensor\Repository\ORM\Sensors\SensorTypeRepositoryInterface;
use App\ESPDeviceSensor\SensorDataServices\NewSensor\NewSensorCreationServiceInterface;
use App\ESPDeviceSensor\SensorDataServices\NewSensor\ReadingTypeCreation\SensorReadingTypeCreationInterface;
use App\ESPDeviceSensor\Voters\SensorVoter;
use App\Form\FormMessages;
use App\Services\CardUserDataService;
use App\Traits\API\HomeAppAPIResponseTrait;
use Doctrine\ORM\ORMException;
use JsonException;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/HomeApp/api/sensors', name: 'devices')]
class AddNewSensorController extends AbstractController
{
    use HomeAppAPIResponseTrait;

    #[Route('/add-new-sensor', name: 'add-new-sensor', methods: [Request::METHOD_POST])]
    public function addNewSensor(
        Request $request,
        NewSensorCreationServiceInterface $newSensorCreationService,
        SensorReadingTypeCreationInterface $readingTypeCreation,
        DeviceRepositoryInterface $deviceRepository,
        SensorTypeRepositoryInterface $sensorTypeRepository,
        CardUserDataService $cardDataService
    ): JsonResponse {
        try {
            $sensorData = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return $this->sendBadRequestJsonResponse(['Request Format not supported']);
        }
        if (empty($sensorData['sensorTypeID'] || $sensorData['deviceNameID'])) {
            return $this->sendBadRequestJsonResponse([APIErrorMessages::MALFORMED_REQUEST_MISSING_DATA]);
        }

        $device = $deviceRepository->findOneById($sensorData['deviceNameID']);
        if (!$device instanceof Devices) {
            return $this->sendBadRequestJsonResponse([
                sprintf(
                    APIErrorMessages::OBJECT_NOT_FOUND,
                    'Device',
                ),
            ]);
        }
        $sensorType = $sensorTypeRepository->findOneById($sensorData['sensorTypeID']);
        if (!$sensorType instanceof SensorType) {
            return $this->sendBadRequestJsonResponse([
                sprintf(
                    APIErrorMessages::OBJECT_NOT_FOUND,
                    'SensorType',
                ),
            ]);
        }

        $newSensorDTO = new NewSensorDTO(
            $sensorData['sensorName'],
            $sensorType,
            $device,
            $this->getUser()
        );
        try {
            $this->denyAccessUnlessGranted(SensorVoter::ADD_NEW_SENSOR, $newSensorDTO);
        } catch (AccessDeniedException) {
            return $this->sendForbiddenAccessJsonResponse([FormMessages::ACCESS_DENIED]);
        }

        $sensor = $newSensorCreationService->createNewSensor($newSensorDTO);
        $sensorCreationErrors = $newSensorCreationService->validateSensor($sensor);

        if (!empty($sensorCreationErrors)) {
            return $this->sendBadRequestJsonResponse($sensorCreationErrors);
        }

        $saveSensor = $newSensorCreationService->saveNewSensor($sensor);

        if ($saveSensor !== true) {
            return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_SAVE_DATA]);
        }

//        dd('s');
        $sensorReadingTypeCreationErrors = $readingTypeCreation->handleSensorReadingTypeCreation($sensor);

        if (!empty($sensorReadingTypeCreationErrors)) {
            return $this->sendBadRequestJsonResponse($sensorReadingTypeCreationErrors);
        }
        try {
            $cardDataService->createNewSensorCard($sensor, $this->getUser());
        } catch (ORMException) {
            return $this->sendInternalServerErrorJsonResponse(['error creating card for user interface but sensor was created successfully']);
        }

        $sensorID = $sensor->getSensorNameID();

        return $this->sendCreatedResourceJsonResponse(['sensorNameID' => $sensorID]);
    }
}
