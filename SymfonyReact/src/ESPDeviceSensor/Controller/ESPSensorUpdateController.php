<?php

namespace App\ESPDeviceSensor\Controller;

use App\ESPDeviceSensor\DTO\Sensor\UpdateSensorReadingDTO;
use App\ESPDeviceSensor\Entity\SensorType;
use App\Traits\API\HomeAppAPIResponseTrait;
use Exception;
use JsonException;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/HomeApp/api/device/', name: 'device')]
class ESPSensorUpdateController extends AbstractController
{
    use HomeAppAPIResponseTrait;

    private ProducerInterface $currentReadingAMQPProducer;

    public const SENSOR_UPDATE_SUCCESS_MESSAGE = 'Sensor data accepted';

    /**
     * @param Request $request
     * @return JsonResponse|Response
     */
    #[Route('esp/update/current-reading', name: 'update-current-reading', methods: [Request::METHOD_PUT, Request::METHOD_POST])]
    public function updateSensorsCurrentReading(
        Request $request,
    ): JsonResponse|Response {
        try {
            $requestData = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return $this->sendBadRequestJsonResponse(['the format sent is not expected, please send requests in JSON']);
        }

        if (empty($requestData['sensorData'])) {
            return $this->sendBadRequestJsonResponse(['you have not provided the correct information to update the sensor']);;
        }

        if (!in_array($requestData['sensorType'], SensorType::ALL_SENSORS, true)) {
            return $this->sendBadRequestJsonResponse(['Sensor type not recognised']);
        }

        $isCallable = [$this->getUser(), 'getDeviceNameID'];

        if (!is_callable($isCallable) || !$this->getUser()?->getDeviceNameID()) {
            return $this->sendBadRequestJsonResponse(['No device id found for device']);
        }
        $deviceId = $this->getUser()?->getDeviceNameID();

        $errors = [];
        foreach ($requestData['sensorData'] as $sensorUpdateData) {
            try {
                $updateReadingDTO = new UpdateSensorReadingDTO(
                    $requestData['sensorType'],
                    $sensorUpdateData['sensorName'],
                    $sensorUpdateData['currentReadings'],
                    $deviceId
                );
                $this->currentReadingAMQPProducer->publish(serialize($updateReadingDTO));
            } catch (Exception $exception) {
                $errors[] = $exception->getMessage();
            }
        }
        if (!empty($errors)) {
            return $this->sendMultiStatusJsonResponse(['Only partial content processed']);
        }

        return $this->sendSuccessfulJsonResponse([self::SENSOR_UPDATE_SUCCESS_MESSAGE]);
    }

    /**
     * @param ProducerInterface $producer
     */
    public function setESPCurrentReadingProducer(ProducerInterface $producer): void
    {
        $this->currentReadingAMQPProducer = $producer;
    }
}
