<?php

namespace App\Sensors\Controller\SensorControllers;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\Devices\Entity\Devices;
use App\Sensors\Builders\MessageDTOBuilders\UpdateSensorCurrentReadingDTOBuilder;
use App\Sensors\Builders\SensorDataDTOBuilders\SensorDataCurrentReadingRequestDTOBuilder;
use App\Sensors\DTO\Request\SensorUpdateRequestDTO;
use App\Sensors\Exceptions\SensorDataCurrentReadingUpdateBuilderException;
use App\Sensors\Exceptions\UpdateCurrentReadingValidationErrorException;
use App\Sensors\SensorServices\SensorReadingUpdate\CurrentReading\CurrentReadingSensorDataRequestHandlerInterface;
use App\Sensors\Voters\SensorVoter;
use App\User\Entity\User;
use Exception;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Service\Attribute\Required;

#[Route(CommonURL::DEVICE_HOMEAPP_API_URL, name: 'sensor-current-reading-update')]
class ESPSensorCurrentReadingUpdateController extends AbstractController
{
    use HomeAppAPITrait;
    use ValidatorProcessorTrait;

    private ProducerInterface $currentReadingAMQPProducer;

    private ProducerInterface $sendCurrentReadingAMQPProducer;

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
        try {
            $this->denyAccessUnlessGranted(SensorVoter::DEVICE_UPDATE_SENSOR_CURRENT_READING);
        } catch (AccessDeniedException) {
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
            );
        } catch (NotEncodableValueException) {
            return $this->sendBadRequestJsonResponse([APIErrorMessages::FORMAT_NOT_SUPPORTED]);
        }

        $validationErrors = $validator->validate($sensorUpdateRequestDTO);
        if ($this->checkIfErrorsArePresent($validationErrors)) {
            return $this->sendBadRequestJsonResponse($this->getValidationErrorAsArray($validationErrors));
        }

        foreach ($sensorUpdateRequestDTO->getSensorData() as $sensorUpdateData) {
            if (!is_array($sensorUpdateData)) {
                $individualSensorRequestValidationErrors[] = SensorDataCurrentReadingUpdateBuilderException::NOT_ARRAY_ERROR_MESSAGE;
                continue;
            }

            $sensorDataCurrentReadingUpdateRequestDTO = SensorDataCurrentReadingRequestDTOBuilder::buildSensorDataCurrentReadingUpdateDTO(
                $sensorUpdateData['sensorName'] ?? null,
                $sensorUpdateData['sensorType'] ?? null,
                $sensorUpdateData['currentReadings'] ?? null,
            );

            $sensorDataPassedValidation = $currentReadingSensorDataRequest->processSensorUpdateData($sensorDataCurrentReadingUpdateRequestDTO, [CurrentReadingSensorDataRequestHandlerInterface::UPDATE_CURRENT_READING]);
            if ($sensorDataPassedValidation === false) {
                continue;
            }
            $readingTypeCurrentReadingDTOs = $currentReadingSensorDataRequest->handleCurrentReadingDTOCreation($sensorDataCurrentReadingUpdateRequestDTO);

            $updateReadingDTO = UpdateSensorCurrentReadingDTOBuilder::buildUpdateSensorCurrentReadingConsumerMessageDTO(
                $sensorDataCurrentReadingUpdateRequestDTO->getSensorType(),
                $sensorDataCurrentReadingUpdateRequestDTO->getSensorName(),
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
            isset($sensorDataCurrentReadingUpdateRequestDTO)
            && empty($individualSensorRequestValidationErrors)
            && empty($currentReadingSensorDataRequest->getErrors())
            && empty($currentReadingSensorDataRequest->getValidationErrors())
            && $currentReadingSensorDataRequest->getReadingTypeRequestAttempt() > 0
            && $currentReadingSensorDataRequest->getReadingTypeRequestAttempt() === count($currentReadingSensorDataRequest->getSuccessfulRequests())
        ) {
            try {
                $normalizedResponse = $this->normalizeResponse($currentReadingSensorDataRequest->getSuccessfulRequests());
                $normalizedResponse = array_map('current', $normalizedResponse);
            } catch (ExceptionInterface) {
                return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_NORMALIZE_RESPONSE]);
            }

            return $this->sendSuccessfulJsonResponse($normalizedResponse, 'All sensor readings handled successfully');
        }

        $mergedErrors = array_merge(
            $individualSensorRequestValidationErrors ?? [],
            $currentReadingSensorDataRequest->getValidationErrors(),
            $currentReadingSensorDataRequest->getErrors(),
        );

        // Complete Failed return
        if (empty($currentReadingSensorDataRequest->getSuccessfulRequests())) {
            try {
                $normalizedResponse = $this->normalizeResponse($mergedErrors);
                if (count($normalizedResponse) > 0) {
                    $normalizedResponse = array_unique(array_map('current', $normalizedResponse));
                }
            } catch (ExceptionInterface) {
                return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_NORMALIZE_RESPONSE]);
            }

            return $this->sendBadRequestJsonResponse($normalizedResponse, APIErrorMessages::COULD_NOT_PROCESS_ANY_CONTENT);
        }

        // Partial Success return
        try {
            $normalizedErrorResponse = $this->normalizeResponse($mergedErrors);
            if (count($normalizedErrorResponse) > 0) {
                $normalizedErrorResponse = array_unique(array_map('current', $normalizedErrorResponse));
            }
            $normalizedSuccessResponse = $this->normalizeResponse(
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
