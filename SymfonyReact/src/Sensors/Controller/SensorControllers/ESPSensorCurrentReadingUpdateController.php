<?php

namespace App\Sensors\Controller\SensorControllers;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
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

        foreach ($sensorUpdateRequestDTO->getSensorData() as $sensorUpdateData) {
            $sensorDataCurrentReadingUpdateDTO = SensorDataCurrentReadingDTOBuilder::buildSensorDataCurrentReadingUpdateDTO($sensorUpdateData);
            $sensorDataPassedValidation = $currentReadingSensorDataRequest->handleSensorUpdateRequest($sensorDataCurrentReadingUpdateDTO);
            if ($sensorDataPassedValidation === false) {
                continue;
            }

            $readingTypeCurrentReadingDTOs = $currentReadingSensorDataRequest->handleCurrentReadingDTOCreation($sensorDataCurrentReadingUpdateDTO);

            $updateReadingDTO = UpdateSensorCurrentReadingDTOBuilder::buildUpdateSensorCurrentReadingConsumerMessageDTO(
                $sensorDataCurrentReadingUpdateDTO->getSensorType(),
                $sensorDataCurrentReadingUpdateDTO->getSensorName(),
                $readingTypeCurrentReadingDTOs,
                $deviceId,
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
            return $this->sendSuccessfulJsonResponse($currentReadingSensorDataRequest->getSuccessfulRequests(), 'All sensor readings handled successfully');
        }

        if (!empty($currentReadingSensorDataRequest->getSuccessfulRequests())) {
            return $this->sendMultiStatusJsonResponse(
                array_merge(
                    $currentReadingSensorDataRequest->getValidationErrors(),
                    $currentReadingSensorDataRequest->getErrors()
                ),
                $currentReadingSensorDataRequest->getSuccessfulRequests(),
                APIErrorMessages::PART_OF_CONTENT_PROCESSED,
            );
        }

        return $this->sendBadRequestJsonResponse(
            array_merge(
                $currentReadingSensorDataRequest->getValidationErrors(),
                $currentReadingSensorDataRequest->getErrors()
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
