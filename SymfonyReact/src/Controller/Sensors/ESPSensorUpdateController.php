<?php

namespace App\Controller\Sensors;

use App\ESPDeviceSensor\DTO\Sensor\UpdateSensorReadingDTO;
use App\Traits\API\HomeAppAPIResponseTrait;
use Exception;
use JsonException;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

#[Route('/HomeApp/api/device/esp/', name: 'device')]
class ESPSensorUpdateController extends AbstractController
{
    use HomeAppAPIResponseTrait;

    private ProducerInterface $currentReadingAMQPProducer;


    /**
     * @param Request $request
     * @param Security $security
     * @return JsonResponse|Response
     */
    #[Route('update/current-reading', name: 'update-current-reading', methods: [Request::METHOD_PUT, Request::METHOD_POST])]
    public function updateSensorsCurrentReading(
        Request $request,
        Security $security,
    ): JsonResponse|Response {
//        try {
            $requestData = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
//        } catch (JsonException) {
//            return $this->sendBadRequestJsonResponse(['the format sent is not expected, please send requests in JSON']);
//        }

        if (empty($requestData['sensorData'])) {
            throw new BadRequestException('you have not provided the correct information to update the sensor');
        }

        $isCallable = [$security->getUser(), 'getDeviceNameID'];

        if (!is_callable($isCallable) || !$security->getUser()->getDeviceNameID()) {
            return $this->sendBadRequestJsonResponse(['No device id found for device']);
        }

        $deviceId = $security->getUser()->getDeviceNameID();

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
//                dd($exception->getMessage());
                $errors[] = $exception->getMessage();
            }
        }
        if (!empty($errors)) {
            return $this->sendMultiStatusJsonResponse();
        }

        return $this->sendSuccessfulJsonResponse(['Sensor data accepted']);
    }

    /**
     * @param ProducerInterface $producer
     */
    public function setESPCurrentReadingProducer(ProducerInterface $producer): void
    {
        $this->currentReadingAMQPProducer = $producer;
    }
}
