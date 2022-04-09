<?php

namespace App\Sensors\Controller\SensorControllers;

use App\API\APIErrorMessages;
use App\API\CommonURL;
use App\API\Traits\HomeAppAPITrait;
use App\Common\Traits\ValidatorProcessorTrait;
use App\Sensors\DTO\Request\SensorUpdateRequestDTO;
use App\Sensors\DTO\Sensor\CurrentReadingDTO\UpdateSensorCurrentReadingConsumerMessageDTO;
use App\Sensors\Entity\SensorType;
use Exception;
use JsonException;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Service\Attribute\Required;

#[Route(CommonURL::DEVICE_HOMEAPP_API_URL, name: 'device')]
class ESPSensorUpdateController extends AbstractController
{
    use HomeAppAPITrait;
    use ValidatorProcessorTrait;

    public const SENSOR_UPDATE_SUCCESS_MESSAGE = 'Sensor data accepted';

    private ProducerInterface $currentReadingAMQPProducer;

    #[Route(
        path: 'esp/update/current-reading',
        name: 'update-current-reading',
        methods: [
            Request::METHOD_PUT,
            Request::METHOD_POST
        ]
    )]
    public function updateSensorsCurrentReading(Request $request, ValidatorInterface $validator): Response
    {
        $sensorUpdateRequestDTO = new SensorUpdateRequestDTO();

        try {
            $this->deserializeRequest(
                $request->getContent(),
                SensorUpdateRequestDTO::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $sensorUpdateRequestDTO]
            );
        } catch (NotEncodableValueException) {
            return $this->sendBadRequestJsonResponse([APIErrorMessages::FORMAT_NOT_SUPPORTED]);
        }

        $validationErrors = $validator->validate($sensorUpdateRequestDTO);

        if ($this->checkIfErrorsArePresent($validationErrors)) {
            return $this->sendBadRequestJsonResponse($this->getValidationErrorAsArray($validationErrors));
        }

        try {
            $requestData = json_decode($request->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return $this->sendBadRequestJsonResponse(['the format sent is not expected, please send requests in JSON']);
        }

        if (empty($sensorUpdateRequestDTO->getSensorData())) {
            return $this->sendBadRequestJsonResponse(['you have not provided the correct information to update the sensor']);;
        }

        if (!in_array($sensorUpdateRequestDTO->getSensorType(), SensorType::ALL_SENSOR_TYPES, true)) {
            return $this->sendBadRequestJsonResponse(['Sensor type not recognised']);
        }

        if (!is_callable([$this->getUser(), 'getDeviceNameID']) || !$this->getUser()?->getDeviceNameID()) {
            return $this->sendBadRequestJsonResponse(['No device id found for device']);
        }
        $deviceId = $this->getUser()?->getDeviceNameID();

        $errors = [];
        foreach ($sensorUpdateRequestDTO->getSensorData() as $sensorUpdateData) {
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
