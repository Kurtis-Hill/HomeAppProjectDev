<?php

namespace App\Controller\Sensor\SensorControllers;

use App\Builders\Sensor\Internal\SensorBuilder;
use App\Builders\Sensor\Response\SensorResponseDTOBuilders\SensorResponseDTOBuilder;
use App\DTOs\RequestDTO;
use App\DTOs\Sensor\Request\AddNewSensorRequestDTO;
use App\Entity\User\User;
use App\Services\API\APIErrorMessages;
use App\Services\API\CommonURL;
use App\Services\Request\RequestQueryParameterHandler;
use App\Services\Sensor\NewReadingType\ReadingTypeCreationInterface;
use App\Services\Sensor\NewSensor\NewSensorCreationInterface;
use App\Services\Sensor\SensorDeletion\SensorDeletionInterface;
use App\Services\UserInterface\Cards\CardCreation\CardCreationHandlerInterface;
use App\Traits\HomeAppAPITrait;
use App\Traits\ValidatorProcessorTrait;
use App\Voters\SensorVoter;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'sensor', name: 'new-sensor')]
class AddNewSensorController extends AbstractController
{
    use HomeAppAPITrait;
    use ValidatorProcessorTrait;

    private LoggerInterface $logger;

    private RequestQueryParameterHandler $requestQueryParameterHandler;

    public function __construct(LoggerInterface $elasticLogger, RequestQueryParameterHandler $requestQueryParameterHandler)
    {
        $this->logger = $elasticLogger;
        $this->requestQueryParameterHandler = $requestQueryParameterHandler;
    }

    #[Route('', name: 'add-new-sensor', methods: [Request::METHOD_POST])]
    public function addNewSensor(
        #[MapRequestPayload]
        AddNewSensorRequestDTO $newSensorRequestDTO,
        NewSensorCreationInterface $newSensorCreationService,
        CardCreationHandlerInterface $cardCreationService,
        ReadingTypeCreationInterface $readingTypeCreation,
        SensorDeletionInterface $deleteSensorService,
        ValidatorInterface $validator,
        SensorBuilder $sensorBuilder,
        #[MapQueryString]
        ?RequestDTO $requestDTO = null,
    ): JsonResponse {
        $requestDTO ??= new RequestDTO();

         $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->sendForbiddenAccessJsonResponse();
        }

        $newSensor = $sensorBuilder->buildNewSensor(
            sensorName: $newSensorRequestDTO->getSensorName(),
            sensorTypeID: $newSensorRequestDTO->getSensorTypeID(),
            deviceID: $newSensorRequestDTO->getDeviceID(),
            createdByID: $user->getUserID(),
            pinNumber: $newSensorRequestDTO->getPinNumber(),
            readingInterval: $newSensorRequestDTO->getReadingInterval(),
        );

        try {
            $this->denyAccessUnlessGranted(SensorVoter::ADD_NEW_SENSOR, $newSensor);
        } catch (AccessDeniedException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        }

        $validationErrors = $validator->validate($newSensor);
        if ($this->checkIfErrorsArePresent($validationErrors)) {
            return $this->sendBadRequestJsonResponse($this->getValidationErrorAsArray($validationErrors));
        }

        if (!empty($sensorCreationErrors)) {
            return $this->sendBadRequestJsonResponse($sensorCreationErrors);
        }

        $saveSensor = $newSensorCreationService->saveSensor($newSensor);
        if ($saveSensor !== true) {
            $this->logger->error(APIErrorMessages::FAILED_TO_SAVE_DATA, ['user' => $this->getUser()->getUserIdentifier()]);

            return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_SAVE_DATA]);
        }

        $sensorReadingTypesCreated = $readingTypeCreation->handleSensorReadingTypeCreation($newSensor);
        foreach ($sensorReadingTypesCreated as $sensorReadingType) {
            $readingTypeValidationErrors = $validator->validate(value: $sensorReadingType, groups: [$newSensor->getSensorTypeObject()::getSensorTypeName()]);
            if ($this->checkIfErrorsArePresent($readingTypeValidationErrors)) {
                $deleteSensorService->deleteSensor($newSensor);

                return $this->sendBadRequestJsonResponse($this->getValidationErrorAsArray($readingTypeValidationErrors));
            }
        }

        $this->logger->info('Created sensor', ['user' => $this->getUser()?->getUserIdentifier()]);

        $errors = $cardCreationService->createUserCardForSensor($newSensor, $user);

        $sensorResponseDTO = SensorResponseDTOBuilder::buildSensorResponseDTO($newSensor);
        try {
            $normalizedResponse = $this->normalize($sensorResponseDTO, [$requestDTO->getResponseType()]);
        } catch (ExceptionInterface) {
            return $this->sendMultiStatusJsonResponse([APIErrorMessages::FAILED_TO_NORMALIZE_RESPONSE]);
        }
        if (!empty($errors)) {
            return $this->sendMultiStatusJsonResponse($errors, $normalizedResponse);
        }

        return $this->sendCreatedResourceJsonResponse($normalizedResponse);
    }
}
