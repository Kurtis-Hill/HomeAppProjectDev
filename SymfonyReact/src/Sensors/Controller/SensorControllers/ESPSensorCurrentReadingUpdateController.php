<?php

namespace App\Sensors\Controller\SensorControllers;

use App\API\APIErrorMessages;
use App\API\CommonURL;
use App\API\Traits\HomeAppAPITrait;
use App\Common\Traits\ValidatorProcessorTrait;
use App\Sensors\Builders\SensorTypeDTOBuilders\SensorDataCurrentReadingDTOBuilder;
use App\Sensors\DTO\Request\CurrentReadingUpdateDTO;
use App\Sensors\DTO\Request\SensorDataCurrentReadingUpdateDTO;
use App\Sensors\DTO\Request\SensorUpdateRequestDTO;
use App\Sensors\DTO\Sensor\CurrentReadingDTO\UpdateSensorCurrentReadingConsumerMessageDTO;
use App\Sensors\Entity\SensorType;
use App\Sensors\Exceptions\SensorReadingUpdateFactoryException;
use App\Sensors\Factories\ORMFactories\SensorReadingType\SensorReadingUpdateFactory;
use App\Sensors\Repository\ORM\Sensors\SensorTypeRepositoryInterface;
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
class ESPSensorCurrentReadingUpdateController extends AbstractController
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
            Request::METHOD_POST,
        ]
    )]
    public function updateSensorsCurrentReading(
        Request $request,
        ValidatorInterface $validator,
        SensorTypeRepositoryInterface $sensorTypeRepository,
        SensorReadingUpdateFactory $sensorReadingUpdateFactory,
    ): Response {
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

        if (empty($sensorUpdateRequestDTO->getSensorData())) {
            return $this->sendBadRequestJsonResponse(['you have not provided the correct information to update the sensor, sensorData empty']);;
        }

        if (!is_callable([$this->getUser(), 'getDeviceNameID']) || !$this->getUser()?->getDeviceNameID()) {
            return $this->sendBadRequestJsonResponse(['You are not supposed to be here']);
        }

        $allSensorTypes = $sensorTypeRepository->getAllSensorTypeNames();
        $deviceId = $this->getUser()?->getDeviceNameID();
        $errors = [];
        $readingTypeCurrentReadingDTOs = [];
        foreach ($sensorUpdateRequestDTO->getSensorData() as $sensorUpdateData) {
            $sensorDataCurrentReadingUpdateDTO = SensorDataCurrentReadingDTOBuilder::buildSensorDataCurrentReadingUpdateDTO($sensorUpdateData);

            $objectValidationErrors = $validator->validate($sensorDataCurrentReadingUpdateDTO);
            if ($this->checkIfErrorsArePresent($objectValidationErrors)) {
                $validationErrors[] = $this->getValidationErrorAsArray($objectValidationErrors);
            }
            if (!in_array($sensorDataCurrentReadingUpdateDTO->getSensorType(), $allSensorTypes, true)) {
                $objectValidationErrors = true;
                $errors[] = [sprintf(APIErrorMessages::OBJECT_NOT_FOUND, 'Sensor type')];
            }
            if ($objectValidationErrors === true || $objectValidationErrors->count() > 0) {
                continue;
            }

            foreach ($sensorDataCurrentReadingUpdateDTO->getCurrentReadings() as $readingType => $currentReading) {
                try {
                    $sensorTypeUpdateDTOBuilder = $sensorReadingUpdateFactory->getReadingTypeUpdateBuilder($readingType);
                } catch (SensorReadingUpdateFactoryException $e) {
                    $errors[] = [$e->getMessage()];
                    continue;
                }
                $readingTypeCurrentReadingDTOs[] = $sensorTypeUpdateDTOBuilder->buildRequestCurrentReadingUpdateDTO($currentReading);
            }

            try {
                $updateReadingDTO = new UpdateSensorCurrentReadingConsumerMessageDTO(
                    $sensorDataCurrentReadingUpdateDTO->getSensorType(),
                    $sensorDataCurrentReadingUpdateDTO->getSensorName(),
                    $readingTypeCurrentReadingDTOs,
                    $deviceId
                );
                $this->currentReadingAMQPProducer->publish(serialize($updateReadingDTO));
                $successfulRequests[] = $sensorDataCurrentReadingUpdateDTO->getSensorName() . 'Sensor data accepted';
            } catch (Exception $exception) {
                $errors[] = $exception->getMessage();
            }
        }
//
//        $errors = array_merge($errors, $validationErrors);
//        if (count($successfulRequests ?? []) === count($sensorUpdateRequestDTO->getSensorData())) {
//            return $this->sendBadRequestJsonResponse(['None of the content could be processed']);
//        }
//        if (!empty($errors)) {
//            return $this->sendMultiStatusJsonResponse($errors, $successfulRequests ?? []);
//        }

        return $this->sendSuccessfulJsonResponse([self::SENSOR_UPDATE_SUCCESS_MESSAGE]);
    }

    #[Required]
    public function setESPCurrentReadingProducer(ProducerInterface $producer): void
    {
        $this->currentReadingAMQPProducer = $producer;
    }
}
