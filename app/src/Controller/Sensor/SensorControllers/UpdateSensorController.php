<?php

namespace App\Controller\Sensor\SensorControllers;

use App\Builders\Sensor\Request\SensorUpdateBuilders\SensorUpdateDTOBuilder;
use App\Builders\Sensor\Response\SensorResponseDTOBuilders\SensorResponseDTOBuilder;
use App\DTOs\Sensor\Request\SensorUpdateDTO\UpdateSensorDetailsRequestDTO;
use App\Entity\Sensor\Sensor;
use App\Exceptions\Common\ValidatorProcessorException;
use App\Exceptions\Sensor\DeviceNotFoundException;
use App\Exceptions\Sensor\DuplicateSensorException;
use App\Services\API\APIErrorMessages;
use App\Services\API\CommonURL;
use App\Services\Request\RequestQueryParameterHandler;
use App\Services\Request\RequestTypeEnum;
use App\Services\Sensor\NewSensor\SensorSavingHandler;
use App\Services\Sensor\UpdateSensor\UpdateSensorInterface;
use App\Traits\HomeAppAPITrait;
use App\Traits\ValidatorProcessorTrait;
use App\Voters\SensorVoter;
use Doctrine\ORM\Exception\ORMException;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'sensor', name: 'update-sensor')]
class UpdateSensorController extends AbstractController
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

    #[Route('/{sensorID}/update', name: 'update-sensor', methods: [Request::METHOD_PUT, Request::METHOD_PATCH])]
    public function updateSensor(
        Sensor $sensor,
        Request $request,
        ValidatorInterface $validator,
        UpdateSensorInterface $updateSensorService,
        SensorSavingHandler $sensorSavingHandler,
        SensorUpdateDTOBuilder $sensorUpdateDTOBuilder,
    ): JsonResponse {
        $updateSensorRequestDTO = new UpdateSensorDetailsRequestDTO();
        try {
            $this->deserializeRequest(
                $request->getContent(),
                UpdateSensorDetailsRequestDTO::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $updateSensorRequestDTO]
            );
        } catch (NotEncodableValueException) {
            return $this->sendBadRequestJsonResponse([APIErrorMessages::FORMAT_NOT_SUPPORTED]);
        }
        $requestValidationErrors = $validator->validate($updateSensorRequestDTO);

        if ($this->checkIfErrorsArePresent($requestValidationErrors)) {
            return $this->sendBadRequestJsonResponse($this->getValidationErrorAsArray($requestValidationErrors));
        }

        try {
            $requestDTO = $this->requestQueryParameterHandler->handlerRequestQueryParameterCreation(
                $request->get(RequestQueryParameterHandler::RESPONSE_TYPE, RequestTypeEnum::ONLY->value),
            );
        } catch (ValidatorProcessorException $e) {
            return $this->sendBadRequestJsonResponse($e->getValidatorErrors());
        }

        $sensorUpdateDTO = $sensorUpdateDTOBuilder->buildSensorUpdateDTOFromRequestDTO(
            $updateSensorRequestDTO,
            $sensor
        );

        try {
            $this->denyAccessUnlessGranted(SensorVoter::UPDATE_SENSOR, $sensorUpdateDTO);
        } catch (AccessDeniedException) {
            return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::ACCESS_DENIED]);
        }

        try {
            $validationErrors = $updateSensorService->handleSensorUpdate($sensorUpdateDTO);
        } catch (DuplicateSensorException|DeviceNotFoundException $e) {
            return $this->sendBadRequestJsonResponse([$e->getMessage()]);
        }

        if (!empty($validationErrors)) {
            return $this->sendBadRequestJsonResponse($validationErrors);
        }

        try {
            $sensorSavingHandler->saveSensor($sensor);
        } catch (ORMException) {
            return $this->sendInternalServerErrorJsonResponse([sprintf(APIErrorMessages::QUERY_FAILURE, 'Device')]);
        }

        $this->logger->info(
            sprintf(
                'sensor: %d updated successfully by user :%d',
                $sensor->getSensorID(),
                $this->getUser()?->getUserID()
            )
        );

        $sensorResponseDTO = SensorResponseDTOBuilder::buildSensorResponseDTO($sensor);
        try {
            $normalizedResponse = $this->normalize($sensorResponseDTO, [$requestDTO->getResponseType()]);
        } catch (ExceptionInterface) {
            return $this->sendMultiStatusJsonResponse([APIErrorMessages::FAILED_TO_NORMALIZE_RESPONSE], ['Sensor Updated']);
        }

        return $this->sendSuccessfullyAddedToBeProcessedJsonResponse($normalizedResponse);
    }
}
