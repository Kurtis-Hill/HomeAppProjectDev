<?php

namespace App\Sensors\Controller\SensorControllers;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\Sensors\Builders\CurrentReadingDTOBuilders\BoolCurrentReadingUpdateDTOBuilder;
use App\Sensors\Builders\MessageDTOBuilders\UpdateSensorCurrentReadingDTOBuilder;
use App\Sensors\Builders\SensorDataDTOBuilders\SensorDataCurrentReadingRequestDTOBuilder;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\BoolCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\SensorUpdateRequestDTO;
use App\Sensors\Entity\SensorTypes\Interfaces\RelayReadingTypeInterface;
use App\Sensors\Exceptions\SensorDataCurrentReadingUpdateBuilderException;
use App\Sensors\Factories\SensorType\SensorTypeRepositoryFactory;
use App\Sensors\Repository\Sensors\SensorRepositoryInterface;
use App\Sensors\SensorServices\SensorReadingUpdate\CurrentReading\CurrentReadingSensorDataRequestHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
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

    #[Route('switch-sensor', name: 'switch-sensor', methods: [Request::METHOD_POST])]
    public function switchSensorAction(
        Request $request,
        ValidatorInterface $validator,
        CurrentReadingSensorDataRequestHandlerInterface $currentReadingSensorDataRequestHandler,
        SensorTypeRepositoryFactory $sensorTypeRepositoryFactory,
        SensorRepositoryInterface $sensorRepository,
        UpdateSensorCurrentReadingDTOBuilder $updateSensorCurrentReadingDTOBuilder,
        EntityManagerInterface $entityManager,
    ): JsonResponse {
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

        $individualSensorRequestValidationErrors = [];
        foreach ($sensorUpdateRequestDTO->getSensorData() as $sensorUpdateData) {
            if (!is_array($sensorUpdateData)) {
                $individualSensorRequestValidationErrors[] = SensorDataCurrentReadingUpdateBuilderException::NOT_ARRAY_ERROR_MESSAGE;
                continue;
            }

            if ($sensorUpdateData['sensorName'] === null) {
                $individualSensorRequestValidationErrors[] = sprintf(APIErrorMessages::OBJECT_NOT_FOUND, 'sensorName');
                continue;
            }

            $sensor = $sensorRepository->findOneBy(['sensorName' => $sensorUpdateData['sensorName']]);

            $sensorDataCurrentReadingUpdateRequestDTO = SensorDataCurrentReadingRequestDTOBuilder::buildSensorDataCurrentReadingUpdateDTO(
                sensorName: $sensorUpdateData['sensorName'],
                sensorType: $sensor?->getSensorTypeObject()->getSensorType(),
                currentReadings: $sensorUpdateData['currentReadings'] ?? null,
            );

            $sensorDataPassedValidationErrors = $validator->validate(value: $sensorDataCurrentReadingUpdateRequestDTO, groups: [CurrentReadingSensorDataRequestHandlerInterface::SEND_UPDATE_CURRENT_READING]);
            if ($this->checkIfErrorsArePresent($sensorDataPassedValidationErrors)) {
                $individualSensorRequestValidationErrors = [
                    $this->getValidationErrorAsArray(
                        $sensorDataPassedValidationErrors
                    ),
                    ...$individualSensorRequestValidationErrors
                ];
                continue;
            }
            if ($sensor === null) {
                $individualSensorRequestValidationErrors[] = [sprintf(APIErrorMessages::OBJECT_NOT_FOUND, 'Sensor')];
                continue;
            }

            $sensorReadingRepository = $sensorTypeRepositoryFactory->getSensorTypeRepository($sensor->getSensorTypeObject()->getSensorType());
            $sensorReadingType = $sensorReadingRepository->findOneBy(['sensor' => $sensor]);

            $readingTypeCurrentReadingDTOs = $currentReadingSensorDataRequestHandler->handleCurrentReadingDTOCreation($sensorDataCurrentReadingUpdateRequestDTO);

            if (empty($readingTypeCurrentReadingDTOs)) {
                continue;
            }

            // just need one as the relay sensor only has one reading type unlike a dht for instance where it has temp and humidity
            $readingTypeCurrentReadingDTO = array_pop($readingTypeCurrentReadingDTOs);
            if (
                ($readingTypeCurrentReadingDTO instanceof BoolCurrentReadingUpdateRequestDTO)
                && $sensorReadingType instanceof RelayReadingTypeInterface
            ) {
                $sensorReadingType->getRelay()->setRequestedReading($readingTypeCurrentReadingDTO->getCurrentReading());
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
            && empty($currentReadingSensorDataRequestHandler->getErrors())
            && $currentReadingSensorDataRequestHandler->getReadingTypeRequestAttempt() > 0
            && $currentReadingSensorDataRequestHandler->getReadingTypeRequestAttempt() === count($currentReadingSensorDataRequestHandler->getSuccessfulRequests())
        ) {
            try {
                $normalizedResponse = $this->normalizeResponse($currentReadingSensorDataRequestHandler->getSuccessfulRequests());
                $normalizedResponse = array_map('current', $normalizedResponse);
            } catch (ExceptionInterface) {
                return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_NORMALIZE_RESPONSE]);
            }

            return $this->sendSuccessfullyAddedToBeProcessedJsonResponse($normalizedResponse, 'All sensor readings handled successfully');
        }

        $errors = array_merge(
            $currentReadingSensorDataRequestHandler->getErrors(),
            $currentReadingSensorDataRequestHandler->getValidationErrors(),
            $individualSensorRequestValidationErrors,
        );

        // Complete Failed return
        if (empty($currentReadingSensorDataRequestHandler->getSuccessfulRequests())) {
            try {
                $normalizedResponse = $this->normalizeResponse($errors);
                if (count($normalizedResponse) > 0) {
                    $normalizedResponse = array_map('current', $normalizedResponse);
                }
            } catch (ExceptionInterface) {
                return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_NORMALIZE_RESPONSE]);
            }

            return $this->sendBadRequestJsonResponse($normalizedResponse, APIErrorMessages::COULD_NOT_PROCESS_ANY_CONTENT);
        }

        // Partial Success return
        try {
            $normalizedErrorResponse = $this->normalizeResponse($errors);
            if (count($normalizedErrorResponse) > 0) {
                $normalizedErrorResponse = array_map('current', $normalizedErrorResponse);
            }
            $normalizedSuccessResponse = $this->normalizeResponse(
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
