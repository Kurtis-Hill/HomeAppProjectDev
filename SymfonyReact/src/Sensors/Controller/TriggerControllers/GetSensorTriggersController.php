<?php

namespace App\Sensors\Controller\TriggerControllers;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\Common\Exceptions\ValidatorProcessorException;
use App\Common\Services\RequestQueryParameterHandler;
use App\Common\Services\RequestTypeEnum;
use App\Common\Validation\Traits\ValidatorProcessorTrait;
use App\Sensors\Builders\Request\GetSensorQueryDTOBuilder\GetSensorQueryDTOBuilder;
use App\Sensors\Builders\Response\TriggerResponseBuilder\SensorTriggerResponseDTOBuilder;
use App\Sensors\Entity\ReadingTypes\BaseSensorReadingType;
use App\Sensors\Exceptions\UserNotAllowedException;
use App\Sensors\Repository\Sensors\SensorRepositoryInterface;
use App\Sensors\Repository\SensorTriggerRepository;
use App\Sensors\SensorServices\SensorReadingTypeFetcher;
use App\User\Entity\User;
use App\User\Services\GroupServices\UserGroupsFinder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'sensor-trigger/get/')]
class GetSensorTriggersController extends AbstractController
{
    use HomeAppAPITrait;
    use ValidatorProcessorTrait;

    public function __construct(private readonly RequestQueryParameterHandler $requestQueryParameterHandler)
    {
    }

    /**
     * @throws AccessDeniedException
     */
    #[Route('all', name: 'get-sensor-triggers', methods: [Request::METHOD_GET])]
    public function getSensorTriggers(
        Request $request,
        SensorTriggerRepository $sensorTriggerRepository,
        SensorRepositoryInterface $sensorRepository,
        UserGroupsFinder $userGroupsFinder,
        SensorTriggerResponseDTOBuilder $sensorTriggerResponseDTOBuilder,
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
                $sensorTriggerResponseDTOs[] = $sensorTriggerResponseDTOBuilder->buildFullSensorTriggerResponseDTO($sensorTrigger);
            } catch (UserNotAllowedException $e) {
                return $this->sendForbiddenAccessJsonResponse([APIErrorMessages::FORBIDDEN_ACTION]);
            }
        }

        try {
            $normalizedResponse = $this->normalizeResponse($sensorTriggerResponseDTOs, [$requestDTO->getResponseType()]);
        } catch (ExceptionInterface) {
            return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_PREPARE_DATA]);
        }

        return $this->sendSuccessfulJsonResponse($normalizedResponse);
    }

//    #[Route('{baseReadingType}/get', name: 'get-single-sensor-triggers', methods: [Request::METHOD_GET])]
//    public function getSensorTriggersByBaseReadingTypeID(BaseSensorReadingType $baseSensorReadingType): JsonResponse
//    {
//
//    }
}