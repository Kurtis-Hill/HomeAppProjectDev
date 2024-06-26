<?php

namespace App\Controller\Sensor\SensorControllers;

use App\Builders\Sensor\Internal\AMPQMessages\CurrentReadingDTOBuilders\UpdateSensorCurrentReadingTransportDTOBuilder;
use App\DTOs\Sensor\Request\SensorUpdateRequestDTO;
use App\Entity\Device\Devices;
use App\Services\API\APIErrorMessages;
use App\Services\API\CommonURL;
use App\Services\Sensor\SensorReadingUpdate\CurrentReading\CurrentReadingSensorDataRequestHandlerInterface;
use App\Traits\HomeAppAPITrait;
use App\Traits\ValidatorProcessorTrait;
use App\Voters\SensorVoter;
use Exception;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Service\Attribute\Required;
use TypeError;
use UnexpectedValueException;

#[Route(CommonURL::DEVICE_HOMEAPP_API_URL, name: 'sensor-current-reading-update')]
class ESPSensorCurrentReadingUpdateController extends AbstractController
{
    use HomeAppAPITrait;
    use ValidatorProcessorTrait;

    private ProducerInterface $currentReadingAMQPProducer;

    private LoggerInterface $logger;

    public function __construct(LoggerInterface $elasticLogger)
    {
        $this->logger = $elasticLogger;
    }

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
        CurrentReadingSensorDataRequestHandlerInterface $currentReadingSensorDataRequest,
    ): Response {
        $isGranted = $this->isGranted(SensorVoter::DEVICE_UPDATE_SENSOR_CURRENT_READING);
        if (!$isGranted) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::FORBIDDEN_ACTION]);
        }

        $device = $this->getUser();
        if (!$device instanceof Devices) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::FORBIDDEN_ACTION]);
        }
        $deviceID = $device->getDeviceID();

        $sensorUpdateRequestDTO = new SensorUpdateRequestDTO();
        try {
            $this->deserializeRequest(
                $request->getContent(),
                SensorUpdateRequestDTO::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $sensorUpdateRequestDTO],
                true,
            );
        } catch (NotEncodableValueException|TypeError|UnexpectedValueException) {
            return $this->sendBadRequestJsonResponse([APIErrorMessages::FORMAT_NOT_SUPPORTED]);
        }
        $errors = $validator->validate(value: $sensorUpdateRequestDTO);
        if ($this->checkIfErrorsArePresent($errors)) {
            return $this->sendBadRequestJsonResponse($this->getValidationErrorAsArray($errors));
        }

        $errors = [];
        foreach ($sensorUpdateRequestDTO->getSensorData() as $sensorDataCurrentReadingUpdateDTO) {
            $error = $validator->validate(value: $sensorDataCurrentReadingUpdateDTO, groups: [CurrentReadingSensorDataRequestHandlerInterface::UPDATE_CURRENT_READING]);
            if ($this->checkIfErrorsArePresent($error)) {
                foreach ($error as $errorString) {
                    $errors[] = $this->getValidationErrorsAsStrings($errorString);
                }
                continue;
            }
            $readingTypeCurrentReadingDTOs = $currentReadingSensorDataRequest->handleCurrentReadingDTOCreation($sensorDataCurrentReadingUpdateDTO);

            $updateReadingDTO = UpdateSensorCurrentReadingTransportDTOBuilder::buildUpdateSensorCurrentReadingConsumerMessageDTO(
                $sensorDataCurrentReadingUpdateDTO->getSensorType(),
                $sensorDataCurrentReadingUpdateDTO->getSensorName(),
                $readingTypeCurrentReadingDTOs,
                $deviceID,
            );
            try {
                $this->currentReadingAMQPProducer->publish(serialize($updateReadingDTO));
            } catch (Exception) {
                $this->logger->emergency('failed to publish UPDATE SENSOR CURRENT READING message to queue', ['user' => $device->getUserIdentifier()]);

                return $this->sendInternalServerErrorJsonResponse([], 'Failed to process request');
            }
        }

        // Success return
        if (
            empty($errors)
            && empty($currentReadingSensorDataRequest->getValidationErrors())
            && $currentReadingSensorDataRequest->getReadingTypeRequestAttempt() > 0
            && $currentReadingSensorDataRequest->getReadingTypeRequestAttempt() === count($currentReadingSensorDataRequest->getSuccessfulRequests())
        ) {
            try {
                $normalizedResponse = $this->normalize($currentReadingSensorDataRequest->getSuccessfulRequests());
                $normalizedResponse = array_map('current', $normalizedResponse);
            } catch (ExceptionInterface) {
                return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_NORMALIZE_RESPONSE]);
            }

            return $this->sendSuccessfulJsonResponse($normalizedResponse, 'All sensor readings handled successfully');
        }

        $mergedErrors = array_merge(
            $errors,
            $currentReadingSensorDataRequest->getValidationErrors(),
        );

        if (!empty($mergedErrors)) {
            $mergedErrors = [...$mergedErrors];
        }

        // Complete Failed return
        if (empty($currentReadingSensorDataRequest->getSuccessfulRequests())) {
            try {
                $normalizedResponse = $this->normalize($mergedErrors);
                if (count($normalizedResponse) > 0) {
                    $normalizedResponse = array_unique($normalizedResponse);
                }
            } catch (ExceptionInterface) {
                return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_NORMALIZE_RESPONSE]);
            }

            return $this->sendBadRequestJsonResponse($normalizedResponse, APIErrorMessages::COULD_NOT_PROCESS_ANY_CONTENT);
        }

        // Partial Success return
        try {
            $normalizedErrorResponse = $this->normalize($mergedErrors);
            if (count($normalizedErrorResponse) > 0) {
                $normalizedErrorResponse = array_unique($normalizedErrorResponse);
            }
            $normalizedSuccessResponse = $this->normalize(
                $currentReadingSensorDataRequest->getSuccessfulRequests(),
            );
            if (count($normalizedSuccessResponse) > 0) {
                $normalizedSuccessResponse = array_map('current', $normalizedSuccessResponse);
            }
        } catch (ExceptionInterface) {
            return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_NORMALIZE_RESPONSE]);
        }

        return $this->sendMultiStatusJsonResponse(
            $normalizedErrorResponse,
            $normalizedSuccessResponse,
            APIErrorMessages::PART_OF_CONTENT_PROCESSED,
        );
    }

    #[Required]
    public function setESPCurrentReadingProducer(ProducerInterface $producer): void
    {
        $this->currentReadingAMQPProducer = $producer;
    }
}
