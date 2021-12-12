<?php

namespace App\ESPDeviceSensor\Controller;

use App\Devices\Entity\Devices;
use App\ESPDeviceSensor\DTO\Sensor\NewSensorDTO;
use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use App\ESPDeviceSensor\Exceptions\DuplicateSensorException;
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
        CardUserDataService $cardDataService
    ): JsonResponse {
        try {
            $sensorData = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            return $this->sendBadRequestJsonResponse(['Request Format not supported']);
        }
        if (empty($sensorData['sensorTypeID'] || $sensorData['deviceNameID'])) {
            return $this->sendBadRequestJsonResponse([FormMessages::FORM_PRE_PROCESS_FAILURE]);
        }
        $newSensorDTO = new NewSensorDTO(
            $sensorData['sensorName'],
            $sensorData['sensorTypeID'],
            $sensorData['deviceNameID']
        );

        $device = $deviceRepository->findOneById($newSensorDTO->getDeviceNameID());

        if (!$device instanceof Devices) {
            return $this->sendBadRequestJsonResponse(['Cannot find device to add sensor too']);
        }
        try {
            $this->denyAccessUnlessGranted(SensorVoter::ADD_NEW_SENSOR, $device);
        } catch (AccessDeniedException) {
            return $this->sendForbiddenAccessJsonResponse([FormMessages::ACCESS_DENIED]);
        }

        try {
            $sensor = $newSensorCreationService->createNewSensor($newSensorDTO);
        } catch (DuplicateSensorException $exception) {
            return $this->sendBadRequestJsonResponse([$exception->getMessage()]);
        } catch (ORMException) {
            return $this->sendInternalServerErrorJsonResponse(['error saving sensor, try again later']);
        }

        if (!empty($newSensorCreationService->getUserInputErrors())) {
            return $this->sendBadRequestJsonResponse($newSensorCreationService->getUserInputErrors());
        }

        $cratedSensorReadingTypes = $readingTypeCreation->handleSensorReadingTypeCreation($sensor);
        if ($cratedSensorReadingTypes === false || !empty($readingTypeCreation->getUserInputErrors())) {
            return $this->sendBadRequestJsonResponse($cardDataService->getUserInputErrors());
        }

        try {
            $cardDataService->createNewSensorCard($sensor, $this->getUser());
        } catch (ORMException) {
            return $this->sendInternalServerErrorJsonResponse(['error creating card for user interface but sensor was created successfully']);
        } catch (RuntimeException $exception) {
            return $this->sendInternalServerErrorJsonResponse([$exception->getMessage()]);
        }

        $sensorID = $sensor->getSensorNameID();

        return $this->sendCreatedResourceJsonResponse(['sensorNameID' => $sensorID]);
    }
}
