<?php

namespace App\ESPDeviceSensor\Controller;

use App\Devices\Entity\Devices;
use App\ESPDeviceSensor\DTO\Sensor\NewSensorDTO;
use App\ESPDeviceSensor\Entity\Sensors;
use App\ESPDeviceSensor\Repository\ORM\Device\DeviceRepositoryInterface;
use App\ESPDeviceSensor\Repository\ORM\Sensors\SensorRepositoryInterface;
use App\ESPDeviceSensor\SensorDataServices\NewSensor\NewSensorCreationServiceInterface;
use App\ESPDeviceSensor\SensorDataServices\NewSensor\ReadingTypeCreation\SensorReadingTypeCreationInterface;
use App\ESPDeviceSensor\Voters\SensorVoter;
use App\Form\FormMessages;
use App\Services\CardUserDataService;
use App\Traits\API\HomeAppAPIResponseTrait;
use JsonException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/HomeApp/api/sensors', name: 'devices')]
class AddNewSensorController extends AbstractController
{
    use HomeAppAPIResponseTrait;


    /**
     * @param Request $request
     * @param NewSensorCreationServiceInterface $newSensorCreationService
     * @param SensorReadingTypeCreationInterface $readingTypeCreation
     * @param DeviceRepositoryInterface $deviceRepository
     * @param SensorRepositoryInterface $sensorRepository
     * @param CardUserDataService $cardDataService
     * @return JsonResponse
     */
    #[Route('/add-new-sensor', name: 'add-new-sensor', methods: [Request::METHOD_POST])]
    public function addNewSensor(
        Request $request,
        NewSensorCreationServiceInterface $newSensorCreationService,
        SensorReadingTypeCreationInterface $readingTypeCreation,
        DeviceRepositoryInterface $deviceRepository,
        SensorRepositoryInterface $sensorRepository,
        CardUserDataService $cardDataService
    ): JsonResponse {
        try {
            $sensorData = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            error_log($exception);
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

        $sensor = $newSensorCreationService->createNewSensor($newSensorDTO);
        if (!empty($newSensorCreationService->getUserInputErrors())) {
            return $this->sendBadRequestJsonResponse($newSensorCreationService->getUserInputErrors());
        }
        if ($sensor === null || !empty($newSensorCreationService->getServerErrors())) {
            return $this->sendInternalServerErrorJsonResponse($newSensorCreationService->getServerErrors());
        }
        if ($sensor instanceof Sensors) {
            $newSensorCard = $cardDataService->createNewSensorCard($sensor, $this->getUser());

            if ($newSensorCard === null || !empty($newSensorCreationService->getServerErrors())) {
                return $this->sendInternalServerErrorJsonResponse($newSensorCreationService->getServerErrors() ?? ['errors' => 'Something went wrong please try again']);
            }

            $readingTypeCreation->handleSensorReadingTypeCreation($sensor);

            if (!empty($newSensorCreationService->getUserInputErrors())) {
                $sensorRepository->remove($sensor);
                $sensorRepository->flush();

                return $this->sendBadRequestJsonResponse($newSensorCreationService->getUserInputErrors());
            }
            if (!empty($newSensorCreationService->getServerErrors())) {
                $sensorRepository->remove($sensor);
                $sensorRepository->flush();

                return $this->sendInternalServerErrorJsonResponse($newSensorCreationService->getServerErrors() ?? ['errors' => 'Something went wrong please try again']);
            }

            $sensorID = $sensor->getSensorNameID();

            return $this->sendCreatedResourceJsonResponse(['sensorNameID' => $sensorID]);
        }

        return $this->sendBadRequestJsonResponse(['Something trying to add a sensor didnt return a sensor, make sure your app is up to date']);
    }
}
