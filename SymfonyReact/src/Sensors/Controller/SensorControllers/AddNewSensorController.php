<?php

namespace App\Sensors\Controller\SensorControllers;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\Common\Exceptions\ValidatorProcessorException;
use App\Common\Services\RequestQueryParameterHandler;
use App\Common\Services\RequestTypeEnum;
use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\Devices\Entity\Devices;
use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use App\Sensors\Builders\SensorCreationBuilders\NewSensorDTOBuilder;
use App\Sensors\Builders\SensorResponseDTOBuilders\SensorResponseDTOBuilder;
use App\Sensors\DTO\Request\AddNewSensorRequestDTO;
use App\Sensors\Entity\SensorType;
use App\Sensors\Exceptions\DeviceNotFoundException;
use App\Sensors\Exceptions\SensorRequestException;
use App\Sensors\Exceptions\SensorTypeNotFoundException;
use App\Sensors\Exceptions\UserNotAllowedException;
use App\Sensors\Repository\Sensors\SensorTypeRepositoryInterface;
use App\Sensors\SensorServices\DeleteSensorService\DeleteSensorHandlerInterface;
use App\Sensors\SensorServices\NewReadingType\ReadingTypeCreationInterface;
use App\Sensors\SensorServices\NewSensor\NewSensorCreationInterface;
use App\Sensors\Voters\SensorVoter;
use App\User\Entity\User;
use App\UserInterface\Services\Cards\CardCreation\CardCreationHandlerInterface;
use Doctrine\ORM\Exception\ORMException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
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


    #[Route('/add', name: 'add-new-sensor', methods: [Request::METHOD_POST])]
    public function addNewSensor(
        Request $request,
        NewSensorCreationInterface $newSensorCreationService,
        ReadingTypeCreationInterface $readingTypeCreation,
        CardCreationHandlerInterface $cardCreationService,
        DeleteSensorHandlerInterface $deleteSensorService,
    ): JsonResponse {
        $newSensorRequestDTO = new AddNewSensorRequestDTO();
        try {
            $this->deserializeRequest(
                $request->getContent(),
                AddNewSensorRequestDTO::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $newSensorRequestDTO]
            );
        } catch (NotEncodableValueException) {
            return $this->sendBadRequestJsonResponse([APIErrorMessages::FORMAT_NOT_SUPPORTED]);
        }

        try {
            $requestDTO = $this->requestQueryParameterHandler->handlerRequestQueryParameterCreation(
                $request->get('responseType', RequestTypeEnum::FULL->value),
            );
        } catch (ValidatorProcessorException $e) {
            return $this->sendBadRequestJsonResponse($e->getValidatorErrors());
        }

        $user = $this->getUser();
        if (!$user instanceof User) {
            return $this->sendForbiddenAccessJsonResponse();
        }

        try {
            $newSensorDTO = $newSensorCreationService->buildNewSensorDTO($newSensorRequestDTO, $user);
        } catch (ORMException $e) {
            $this->logger->error($e->getMessage(), ['user' => $user->getUserIdentifier()]);

            return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::QUERY_FAILURE, 'Sensor data']);
        } catch (SensorRequestException $e) {
            return $this->sendBadRequestJsonResponse($e->getValidationErrors());
        }
        catch (DeviceNotFoundException|SensorTypeNotFoundException $e) {
            return $this->sendBadRequestJsonResponse([$e->getMessage()]);
        }

        try {
            $this->denyAccessUnlessGranted(SensorVoter::ADD_NEW_SENSOR, $newSensorDTO);
        } catch (AccessDeniedException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        }
        try {
            $sensorCreationErrors = $newSensorCreationService->processNewSensor($newSensorDTO);
        } catch (UserNotAllowedException $exception) {
            return $this->sendForbiddenAccessJsonResponse([$exception->getMessage()]);
        }

        if (!empty($sensorCreationErrors)) {
            return $this->sendBadRequestJsonResponse($sensorCreationErrors);
        }

        $sensor = $newSensorDTO->getSensor();

        $saveSensor = $newSensorCreationService->saveSensor($sensor);
        if ($saveSensor !== true) {
            $this->logger->error(APIErrorMessages::FAILED_TO_SAVE_DATA, ['user' => $this->getUser()->getUserIdentifier()]);

            return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_SAVE_DATA]);
        }

        $sensorReadingTypeCreationErrors = $readingTypeCreation->handleSensorReadingTypeCreation($sensor);
        if (!empty($sensorReadingTypeCreationErrors)) {
            try {
                $deleteSensorService->deleteSensor($sensor);
            } catch (ORMException $e) {
                $this->logger->error('Failed to create sensor reading types for sensor', ['sensor' => $sensor->getSensorID(), 'stack' => $e->getTrace()]);
            }

            return $this->sendBadRequestJsonResponse($sensorReadingTypeCreationErrors);
        }

        $errors = $cardCreationService->createUserCardForSensor($sensor, $this->getUser());
        if (!empty($errors)) {
            return $this->sendInternalServerErrorJsonResponse($errors);
        }

        $sensorResponseDTO = SensorResponseDTOBuilder::buildDetailedResponseDTO($sensor);
        try {
            $normalizedResponse = $this->normalizeResponse($sensorResponseDTO, [$requestDTO->getResponseType()]);
        } catch (ExceptionInterface) {
            return $this->sendMultiStatusJsonResponse([APIErrorMessages::FAILED_TO_NORMALIZE_RESPONSE]);
        }
        $this->logger->info('Created sensor', ['user' => $this->getUser()?->getUserIdentifier()]);

        return $this->sendCreatedResourceJsonResponse($normalizedResponse);
    }
}
