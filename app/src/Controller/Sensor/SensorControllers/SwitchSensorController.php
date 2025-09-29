<?php

namespace App\Controller\Sensor\SensorControllers;

use App\Builders\Sensor\Internal\AMPQMessages\CurrentReadingDTOBuilders\BoolCurrentReadingUpdateDTOBuilder;
use App\Builders\Sensor\Internal\AMPQMessages\CurrentReadingDTOBuilders\UpdateSensorCurrentReadingTransportDTOBuilder;
use App\Builders\Sensor\Request\SensorDataDTOBuilders\SensorDataCurrentReadingRequestDTOBuilder;
use App\DTOs\Sensor\Request\CurrentReadingRequest\ReadingTypes\BoolCurrentReadingUpdateRequestDTO;
use App\DTOs\Sensor\Request\CurrentReadingRequest\SensorDataCurrentReadingUpdateRequestDTO;
use App\DTOs\Sensor\Request\SensorUpdateRequestDTO;
use App\Entity\Sensor\SensorTypes\Interfaces\RelayReadingTypeInterface;
use App\Exceptions\Sensor\ReadingTypeNotFoundException;
use App\Exceptions\Sensor\SensorDataCurrentReadingUpdateBuilderException;
use App\Repository\Sensor\ReadingType\ORM\RelayRepository;
use App\Repository\Sensor\Sensors\SensorRepositoryInterface;
use App\Services\API\APIErrorMessages;
use App\Services\API\CommonURL;
use App\Services\Sensor\SensorReadingUpdate\CurrentReading\CurrentReadingSensorDataRequestHandlerInterface;
use App\Traits\HomeAppAPITrait;
use App\Traits\ValidatorProcessorTrait;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Service\Attribute\Required;

#[Route(CommonURL::DEVICE_HOMEAPP_API_URL)]
class SwitchSensorController extends AbstractController
{
    use HomeAppAPITrait;
    use ValidatorProcessorTrait;

    private ProducerInterface $sendCurrentReadingAMQPProducer;

    private LoggerInterface $logger;

    public function __construct(LoggerInterface $elasticLogger)
    {
        $this->logger = $elasticLogger;
    }

    /**
     * @throws SensorDataCurrentReadingUpdateBuilderException
     * @throws ReadingTypeNotFoundException
     */
    #[Route('switch-sensor', name: 'switch-sensor', methods: [Request::METHOD_POST])]
    public function switchSensorAction(
        ValidatorInterface $validator,
        CurrentReadingSensorDataRequestHandlerInterface $currentReadingSensorDataRequestHandler,
        SensorRepositoryInterface $sensorRepository,
        UpdateSensorCurrentReadingTransportDTOBuilder $updateSensorCurrentReadingDTOBuilder,
        EntityManagerInterface $entityManager,
        RelayRepository $relayRepository,
        #[MapRequestPayload]
        SensorUpdateRequestDTO $sensorUpdateRequestDTO,
    ): JsonResponse {
        $individualSensorRequestValidationErrors = [];
        foreach ($sensorUpdateRequestDTO->getSensorData() as $sensorUpdateData) {
            if (!$sensorUpdateData instanceof SensorDataCurrentReadingUpdateRequestDTO ) {
                $individualSensorRequestValidationErrors = [
                    SensorDataCurrentReadingUpdateBuilderException::NOT_ARRAY_ERROR_MESSAGE,
                    ...$individualSensorRequestValidationErrors,
                ];
                continue;
            }

            if ($sensorUpdateData->getSensorName() === null) {
                $individualSensorRequestValidationErrors = [
                    sprintf(APIErrorMessages::OBJECT_NOT_FOUND, 'sensorName'),
                    ...$individualSensorRequestValidationErrors,
                ];
                continue;
            }

            $sensor = $sensorRepository->findOneBy(['sensorName' => $sensorUpdateData->getSensorName()]);

            $sensorDataCurrentReadingUpdateRequestDTO = SensorDataCurrentReadingRequestDTOBuilder::buildSensorDataCurrentReadingUpdateRequestDTO(
                sensorName: $sensorUpdateData->getSensorName(),
                sensorType: $sensor?->getSensorTypeObject()::getSensorTypeName(),
                currentReadings: $sensorUpdateData->getCurrentReadings() ?? null,
            );

            $sensorDataPassedValidationErrors = $validator->validate(
                value: $sensorDataCurrentReadingUpdateRequestDTO,
                groups: [CurrentReadingSensorDataRequestHandlerInterface::SEND_UPDATE_CURRENT_READING]
            );
            if ($this->checkIfErrorsArePresent($sensorDataPassedValidationErrors)) {
                foreach ($sensorDataPassedValidationErrors as $error) {
                    $individualSensorRequestValidationErrors = [
                        $this->getValidationErrorsAsStrings(
                            $error
                        ),
                        ...$individualSensorRequestValidationErrors,
                    ];
                }
                continue;
            }
            if ($sensor === null) {
                $individualSensorRequestValidationErrors = [
                    sprintf(APIErrorMessages::OBJECT_NOT_FOUND, 'Sensor'),
                    ...$individualSensorRequestValidationErrors,
                ];
                continue;
            }

            $readingTypeCurrentReadingDTOs = $currentReadingSensorDataRequestHandler->handleCurrentReadingDTOCreation($sensorDataCurrentReadingUpdateRequestDTO);

            if (empty($readingTypeCurrentReadingDTOs)) {
                continue;
            }

            // just need one as the relay sensor only has one reading type unlike a dht for instance where it has temp and humidity
            $readingTypeCurrentReadingDTO = array_pop($readingTypeCurrentReadingDTOs);
            if (
                ($readingTypeCurrentReadingDTO instanceof BoolCurrentReadingUpdateRequestDTO)
                && $sensor->getSensorTypeObject() instanceof RelayReadingTypeInterface
            ) {
                $relay = $relayRepository->findBySensorID($sensor->getSensorID())[0];
                if (!$relay) {
                    throw new ReadingTypeNotFoundException('Relay reading type not found');
                }
                $relay->setRequestedReading($readingTypeCurrentReadingDTO->getCurrentReading());
            }

            $updateReadingDTO = $updateSensorCurrentReadingDTOBuilder->buildSensorSwitchRequestConsumerMessageDTO(
                $sensor->getSensorID(),
                BoolCurrentReadingUpdateDTOBuilder::buildCurrentReadingUpdateDTO(
                    $readingTypeCurrentReadingDTO->getReadingType(),
                    $readingTypeCurrentReadingDTO->getCurrentReading(),
                ),
            );

            try {
                $this->sendCurrentReadingAMQPProducer->publish(serialize($updateReadingDTO));
            } catch (Exception) {
                $this->logger->emergency('failed to publish REQUEST UPDATE SENSOR CURRENT READING message to queue', ['user' => $this->getUser()?->getUserIdentifier()]);

                return $this->sendInternalServerErrorJsonResponse([], 'Failed to process request');
            }
        }
        $entityManager->flush();

        // Success return
        if (
            isset($sensorDataCurrentReadingUpdateRequestDTO)
            && empty($individualSensorRequestValidationErrors)
            && empty($currentReadingSensorDataRequestHandler->getValidationErrors())
            && $currentReadingSensorDataRequestHandler->getReadingTypeRequestAttempt() > 0
            && $currentReadingSensorDataRequestHandler->getReadingTypeRequestAttempt() === count($currentReadingSensorDataRequestHandler->getSuccessfulRequests())
        ) {
            try {
                $normalizedResponse = $this->normalize($currentReadingSensorDataRequestHandler->getSuccessfulRequests());
                $normalizedResponse = array_map('current', $normalizedResponse);
            } catch (ExceptionInterface) {
                return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_NORMALIZE_RESPONSE]);
            }

            return $this->sendSuccessfullyAddedToBeProcessedJsonResponse($normalizedResponse, 'All sensor readings handled successfully');
        }

        $errors = array_merge(
            $currentReadingSensorDataRequestHandler->getValidationErrors(),
            $individualSensorRequestValidationErrors,
        );

        // Complete Failed return
        if (empty($currentReadingSensorDataRequestHandler->getSuccessfulRequests())) {
            try {
                $normalizedResponse = $this->normalize($errors);
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
            $normalizedErrorResponse = $this->normalize($errors);
            if (count($normalizedErrorResponse) > 0) {
                $normalizedErrorResponse = array_unique($normalizedErrorResponse);
            }
            $normalizedSuccessResponse = $this->normalize(
                $currentReadingSensorDataRequestHandler->getSuccessfulRequests(),
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
    public function setESPSendCurrentReadingProducer(ProducerInterface $producer): void
    {
        $this->sendCurrentReadingAMQPProducer = $producer;
    }
}
