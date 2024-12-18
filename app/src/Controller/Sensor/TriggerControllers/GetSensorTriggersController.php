<?php

namespace App\Controller\Sensor\TriggerControllers;

use App\Builders\Sensor\Request\GetSensorQueryDTOBuilder\GetSensorQueryDTOBuilder;
use App\Builders\Sensor\Response\TriggerResponseBuilder\SensorTriggerResponseDTOBuilder;
use App\Entity\Sensor\SensorTrigger;
use App\Entity\User\User;
use App\Exceptions\Common\ValidatorProcessorException;
use App\Exceptions\Sensor\UserNotAllowedException;
use App\Repository\Sensor\Sensors\SensorRepositoryInterface;
use App\Repository\Sensor\SensorTriggerRepository;
use App\Services\API\APIErrorMessages;
use App\Services\API\CommonURL;
use App\Services\Request\RequestQueryParameterHandler;
use App\Services\Request\RequestTypeEnum;
use App\Services\Sensor\SensorReadingTypeFetcher;
use App\Services\User\GroupServices\UserGroupsFinder;
use App\Traits\HomeAppAPITrait;
use App\Traits\ValidatorProcessorTrait;
use App\Voters\SensorVoter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'sensor-trigger')]
class GetSensorTriggersController extends AbstractController
{
    use HomeAppAPITrait;
    use ValidatorProcessorTrait;

    public function __construct(private readonly RequestQueryParameterHandler $requestQueryParameterHandler, private SensorTriggerResponseDTOBuilder $sensorTriggerResponseDTOBuilder)
    {
    }

    /**
     * @throws AccessDeniedException
     */
    #[Route('', name: 'get-sensor-triggers', methods: [Request::METHOD_GET])]
    public function getSensorTriggers(
        Request $request,
        SensorTriggerRepository $sensorTriggerRepository,
        SensorRepositoryInterface $sensorRepository,
        UserGroupsFinder $userGroupsFinder,
        SensorReadingTypeFetcher $sensorReadingTypeFetcher,
    ): JsonResponse {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException(APIErrorMessages::FORBIDDEN_ACTION);
        }
        try {
            $requestDTO = $this->requestQueryParameterHandler->handlerRequestQueryParameterCreation(
                $request->get(RequestQueryParameterHandler::RESPONSE_TYPE, RequestTypeEnum::FULL->value),
            );
        } catch (ValidatorProcessorException $e) {
            return $this->sendBadRequestJsonResponse($e->getValidatorErrors());
        }

        $getSensorQueryParams = GetSensorQueryDTOBuilder::buildGetSensorQueryDTO(
            limit: 1000,
            groupIDs: $userGroupsFinder->getGroupIDs($user),
        );
        $usersSensors = $sensorRepository->findSensorsByQueryParameters($getSensorQueryParams);

        $baseReadingTypeIDs = $sensorReadingTypeFetcher->fetchBaseReadingTypeIDsFromSensors($usersSensors);

        $sensorTriggers = $sensorTriggerRepository->findAllSensorTriggersForBaseReadingIDs($baseReadingTypeIDs);

        $sensorTriggerResponseDTOs = [];
        foreach ($sensorTriggers as $sensorTrigger) {
            try {
                $sensorTriggerResponseDTOs[] = $this->sensorTriggerResponseDTOBuilder->buildFullSensorTriggerResponseDTO($sensorTrigger);
            } catch (UserNotAllowedException $e) {
                return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::FORBIDDEN_ACTION]);
            }
        }

        try {
            $normalizedResponse = $this->normalize($sensorTriggerResponseDTOs, [$requestDTO->getResponseType()]);
        } catch (ExceptionInterface) {
            return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_PREPARE_DATA]);
        }

        return $this->sendSuccessfulJsonResponse($normalizedResponse);
    }

    #[Route('/{sensorTrigger}', name: 'get-single-sensor-triggers', methods: [Request::METHOD_GET])]
    public function getSensorTriggersByBaseReadingTypeID(SensorTrigger $sensorTrigger): JsonResponse
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            throw $this->createAccessDeniedException(APIErrorMessages::FORBIDDEN_ACTION);
        }

        $allowedToViewTrigger = $this->isGranted(SensorVoter::CAN_GET_SENSOR_TRIGGERS, $sensorTrigger);

        if (!$allowedToViewTrigger) {
            throw $this->createAccessDeniedException(APIErrorMessages::FORBIDDEN_ACTION);
        }

        $triggerResponseDTO = $this->sensorTriggerResponseDTOBuilder->buildFullSensorTriggerResponseDTO($sensorTrigger);

        try {
            $normalizedResponse = $this->normalize($triggerResponseDTO, [RequestTypeEnum::FULL->value]);
        } catch (ExceptionInterface) {
            return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_PREPARE_DATA]);
        }

        return $this->sendSuccessfulJsonResponse($normalizedResponse);
    }
}
