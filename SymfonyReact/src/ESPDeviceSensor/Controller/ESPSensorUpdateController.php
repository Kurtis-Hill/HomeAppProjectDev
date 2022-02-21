<?php

namespace App\ESPDeviceSensor\Controller;

use App\API\CommonURL;
use App\API\Traits\HomeAppAPITrait;
use App\ESPDeviceSensor\DTO\Sensor\CurrentReadingDTO\UpdateSensorCurrentReadingConsumerMessageDTO;
use App\ESPDeviceSensor\Entity\SensorType;
use Exception;
use JsonException;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Service\Attribute\Required;

#[Route(CommonURL::DEVICE_HOMEAPP_API_URL, name: 'device')]
class ESPSensorUpdateController extends AbstractController
{
    use HomeAppAPITrait;

    public const SENSOR_UPDATE_SUCCESS_MESSAGE = 'Sensor data accepted';

    private ProducerInterface $currentReadingAMQPProducer;

    #[Route(
        'esp/update/current-reading',
        name: 'update-current-reading',
        methods: [
            Request::METHOD_PUT,
            Request::METHOD_POST
        ]
    )]
    public function updateSensorsCurrentReading(Request $request): Response
    {
        try {
            $requestData = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return $this->sendBadRequestJsonResponse(['the format sent is not expected, please send requests in JSON']);
        }

        if (empty($requestData['sensorData'])) {
            return $this->sendBadRequestJsonResponse(['you have not provided the correct information to update the sensor']);;
        }

        if (!in_array($requestData['sensorType'], SensorType::ALL_SENSOR_TYPES, true)) {
            return $this->sendBadRequestJsonResponse(['Sensor type not recognised']);
        }

        if (!is_callable([$this->getUser(), 'getDeviceNameID']) || !$this->getUser()?->getDeviceNameID()) {
            return $this->sendBadRequestJsonResponse(['No device id found for device']);
        }
        $deviceId = $this->getUser()?->getDeviceNameID();

        $errors = [];
        foreach ($requestData['sensorData'] as $sensorUpdateData) {
            try {
                $updateReadingDTO = new UpdateSensorCurrentReadingConsumerMessageDTO(
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
        if (count($errors) === count($requestData['sensorData'])) {
            return $this->sendBadRequestJsonResponse(['None of the update requests could be processed']);
        }
        if (!empty($errors)) {
            return $this->sendMultiStatusJsonResponse(['Only part of the content could be processed']);
        }

        return $this->sendSuccessfulJsonResponse([self::SENSOR_UPDATE_SUCCESS_MESSAGE]);
    }

    #[Required]
    public function setESPCurrentReadingProducer(ProducerInterface $producer): void
    {
        $this->currentReadingAMQPProducer = $producer;
    }
}
