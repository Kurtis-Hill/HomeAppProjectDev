<?php

namespace App\Sensors\Controller\SensorControllers;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\Common\Traits\ValidatorProcessorTrait;
use App\Devices\Entity\Devices;
use App\ErrorLogs;
use App\Sensors\Builders\MessageDTOBuilders\UpdateSensorCurrentReadingDTOBuilder;
use App\Sensors\Builders\SensorDataDTOBuilders\SensorDataCurrentReadingDTOBuilder;
use App\Sensors\DTO\Request\SensorUpdateRequestDTO;
use App\Sensors\SensorDataServices\SensorReadingUpdate\CurrentReading\CurrentReadingSensorDataRequestHandlerInterface;
use App\Sensors\Voters\SensorVoter;
use Exception;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Service\Attribute\Required;

#[Route(CommonURL::DEVICE_HOMEAPP_API_URL, name: 'device')]
class ESPSensorCurrentReadingUpdateController extends AbstractController
{
    use HomeAppAPITrait;
    use ValidatorProcessorTrait;

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
        CurrentReadingSensorDataRequestHandlerInterface $currentReadingSensorDataRequest,
    ): Response {
        try {
            $this->denyAccessUnlessGranted(SensorVoter::UPDATE_SENSOR_CURRENT_READING);
        } catch (AccessDeniedException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::FORBIDDEN_ACTION]);
        }
        $deviceID = $this->getUser()?->getDeviceNameID();

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

        foreach ($sensorUpdateRequestDTO->getSensorData() as $sensorUpdateData) {
            $sensorDataCurrentReadingUpdateDTO = SensorDataCurrentReadingDTOBuilder::buildSensorDataCurrentReadingUpdateDTO($sensorUpdateData);
            $sensorDataPassedValidation = $currentReadingSensorDataRequest->processSensorUpdateData($sensorDataCurrentReadingUpdateDTO);
            if ($sensorDataPassedValidation === false) {
                continue;
            }

            $readingTypeCurrentReadingDTOs = $currentReadingSensorDataRequest->handleCurrentReadingDTOCreation($sensorDataCurrentReadingUpdateDTO);

            $updateReadingDTO = UpdateSensorCurrentReadingDTOBuilder::buildUpdateSensorCurrentReadingConsumerMessageDTO(
                $sensorDataCurrentReadingUpdateDTO->getSensorType(),
                $sensorDataCurrentReadingUpdateDTO->getSensorName(),
                $readingTypeCurrentReadingDTOs,
                $deviceID,
            );
            try {
                $this->currentReadingAMQPProducer->publish(serialize($updateReadingDTO));
            } catch (Exception) {
                return $this->sendInternalServerErrorJsonResponse([], 'Failed to process request');
            }
        }

        if (
            isset($sensorDataCurrentReadingUpdateDTO)
            && empty($currentReadingSensorDataRequest->getErrors())
            && empty($currentReadingSensorDataRequest->getValidationErrors())
            && $currentReadingSensorDataRequest->getReadingTypeRequestAttempt() === count($currentReadingSensorDataRequest->getSuccessfulRequests())
        ) {
            try {
                $normalizedResponse = $this->normalizeResponse($currentReadingSensorDataRequest->getSuccessfulRequests());
            } catch (ExceptionInterface) {
                return $this->sendMultiStatusJsonResponse([APIErrorMessages::FAILED_TO_NORMALIZE_RESPONSE]);
            }

            return $this->sendSuccessfulJsonResponse($normalizedResponse, 'All sensor readings handled successfully');
        }

        if (empty($currentReadingSensorDataRequest->getSuccessfulRequests())) {
            return $this->sendBadRequestJsonResponse(
                array_merge(
                    $currentReadingSensorDataRequest->getValidationErrors(),
                    $currentReadingSensorDataRequest->getErrors()
                ),
                APIErrorMessages::COULD_NOT_PROCESS_ANY_CONTENT
            );
        }

        try {
            $normalizedErrorResponse = $this->normalizeResponse(
                array_merge(
                    $currentReadingSensorDataRequest->getValidationErrors(),
                    $currentReadingSensorDataRequest->getErrors()
                ),
            );
            $normalizedSuccessResponse = $this->normalizeResponse(
                $currentReadingSensorDataRequest->getSuccessfulRequests(),
            );
        } catch (ExceptionInterface) {
            return $this->sendMultiStatusJsonResponse([APIErrorMessages::FAILED_TO_NORMALIZE_RESPONSE]);
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
