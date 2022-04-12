<?php

namespace App\Sensors\Controller\SensorControllers;

use App\API\APIErrorMessages;
use App\API\CommonURL;
use App\API\Traits\HomeAppAPITrait;
use App\Common\Traits\ValidatorProcessorTrait;
use App\Devices\Entity\Devices;
use App\ErrorLogs;
use App\Sensors\Builders\MessageDTOBuilders\UpdateSensorCurrentReadingDTOBuilder;
use App\Sensors\Builders\SensorTypeDTOBuilders\SensorDataCurrentReadingDTOBuilder;
use App\Sensors\DTO\Request\SensorUpdateRequestDTO;
use App\Sensors\SensorDataServices\SensorReadingUpdate\CurrentReading\CurrentReadingSensorDataRequestHandlerInterface;
use Exception;
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

    public const SENSOR_UPDATE_SUCCESS_MESSAGE = '%s data accepted for sensor %s';

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
        CurrentReadingSensorDataRequestHandlerInterface $currentReadingSensorDataRequestHandler,
    ): Response {
        if (!$this->getUser() instanceof Devices) {
            return $this->sendBadRequestJsonResponse(['You are not supposed to be here']);
        }
        $deviceId = $this->getUser()?->getDeviceNameID();

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

//        dd('asdsdf', $sensorUpdateRequestDTO);
        $readingTypeCurrentReadingDTOs = [];
        $successfulRequests = [];
        $readingTypeRequestAttempt = 0;
        foreach ($sensorUpdateRequestDTO->getSensorData() as $sensorUpdateData) {
            $sensorDataCurrentReadingUpdateDTO = SensorDataCurrentReadingDTOBuilder::buildSensorDataCurrentReadingUpdateDTO($sensorUpdateData);
//            dd('asd');
//            dd($sensorDataCurrentReadingUpdateDTO, $sensorUpdateData);
            $sensorPassedValidation = $currentReadingSensorDataRequestHandler->validateSensorDataRequest($sensorDataCurrentReadingUpdateDTO);
            if ($sensorPassedValidation === false) {
                continue;
            }

            foreach ($sensorDataCurrentReadingUpdateDTO->getCurrentReadings() as $readingType => $currentReading) {
                ++$readingTypeRequestAttempt;
                $sensorTypeUpdateDTOBuilder = $currentReadingSensorDataRequestHandler->getSensorTypeUpdateDTOBuilder($readingType);
                if ($sensorTypeUpdateDTOBuilder === null) {
                    continue;
                }
                $readingTypeCurrentReadingDTO = $sensorTypeUpdateDTOBuilder->buildRequestCurrentReadingUpdateDTO($currentReading);

                $sensorTypeReadingValidationPassed = $currentReadingSensorDataRequestHandler->validateSensorTypeDTO(
                    $readingTypeCurrentReadingDTO,
                    $sensorDataCurrentReadingUpdateDTO->getSensorType()
                );
                if ($sensorTypeReadingValidationPassed === false) {
                    continue;
                }
                $readingTypeCurrentReadingDTOs[] = $sensorTypeUpdateDTOBuilder->buildRequestCurrentReadingUpdateDTO($currentReading);
                $successfulRequests[] = sprintf(self::SENSOR_UPDATE_SUCCESS_MESSAGE, $readingTypeCurrentReadingDTO->getReadingType(), $sensorDataCurrentReadingUpdateDTO->getSensorName());
            }
//dd($currentReadingSensorDataRequestHandler->getValidationErrors());
            $updateReadingDTO = UpdateSensorCurrentReadingDTOBuilder::buildUpdateSensorCurrentReadingConsumerMessageDTO(
                $sensorDataCurrentReadingUpdateDTO->getSensorType(),
                $sensorDataCurrentReadingUpdateDTO->getSensorName(),
                $readingTypeCurrentReadingDTOs,
                $deviceId,
            );
            try {
                $this->currentReadingAMQPProducer->publish(serialize($updateReadingDTO));
            } catch (Exception $exception) {
                error_log($exception->getMessage(), ErrorLogs::SERVER_ERROR_LOG_LOCATION);
            }
        }

        if (
            isset($sensorDataCurrentReadingUpdateDTO)
            && empty($currentReadingSensorDataRequestHandler->getErrors())
            && empty($currentReadingSensorDataRequestHandler->getValidationErrors())
            && $readingTypeRequestAttempt === count($successfulRequests ?? [])
        ) {
            return $this->sendSuccessfulJsonResponse($successfulRequests ?? [], 'All sensor readings handled successfully');
        }

        if (!empty($successfulRequests)) {
            return $this->sendMultiStatusJsonResponse(
                array_merge(
                    $currentReadingSensorDataRequestHandler->getValidationErrors(),
                    $currentReadingSensorDataRequestHandler->getErrors()
                ),
                $successfulRequests,
                APIErrorMessages::PART_OF_CONTENT_PROCESSED,
            );
        }

        return $this->sendBadRequestJsonResponse(
            array_merge(
                $currentReadingSensorDataRequestHandler->getValidationErrors(),
                $currentReadingSensorDataRequestHandler->getErrors()
            ),
            APIErrorMessages::COULD_NOT_PROCESS_ANY_CONTENT
        );

    }

    #[Required]
    public function setESPCurrentReadingProducer(ProducerInterface $producer): void
    {
        $this->currentReadingAMQPProducer = $producer;
    }
}
