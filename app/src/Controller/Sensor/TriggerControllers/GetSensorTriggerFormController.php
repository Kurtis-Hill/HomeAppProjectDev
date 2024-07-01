<?php

namespace App\Controller\Sensor\TriggerControllers;

use App\Builders\Operator\OperatorResponseDTOBuilder;
use App\Builders\Sensor\Request\GetSensorQueryDTOBuilder\GetSensorQueryDTOBuilder;
use App\Builders\Sensor\Response\SensorReadingTypeResponseBuilders\Bool\RelayResponseDTOBuilder;
use App\Builders\Sensor\Response\SensorResponseDTOBuilders\SensorResponseDTOBuilder;
use App\Builders\Sensor\Response\TriggerResponseBuilder\TriggerFormEncapsulationDTOBuilder;
use App\Builders\Sensor\Response\TriggerResponseBuilder\TriggerTypeResponseBuilder;
use App\Entity\User\User;
use App\Repository\Common\ORM\OperatorRepository;
use App\Repository\Sensor\ReadingType\ORM\RelayRepository;
use App\Repository\Sensor\Sensors\ORM\SensorRepository;
use App\Repository\Sensor\TriggerTypeRepository;
use App\Services\API\APIErrorMessages;
use App\Services\API\CommonURL;
use App\Services\Request\RequestTypeEnum;
use App\Services\User\GroupServices\UserGroupsFinder;
use App\Traits\HomeAppAPITrait;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[Route(CommonURL::USER_HOMEAPP_API_URL . 'sensor-trigger/form')]
class GetSensorTriggerFormController extends AbstractController
{
    use HomeAppAPITrait;

    #[Route('/get', name: 'get-sensor-trigger-form', methods: [Request::METHOD_GET])]
    public function getSensorTriggerForm(
        OperatorRepository $operatorRepository,
        SensorRepository $sensorRepository,
        UserGroupsFinder $userGroupsFinder,
        SensorResponseDTOBuilder $sensorResponseDTOBuilder,
        TriggerTypeRepository $triggerTypeRepository,
        RelayRepository $relayRepository,
        RelayResponseDTOBuilder $relayResponseDTOBuilder,
    ): JsonResponse {
        $user = $this->getUser();
        if (!$user instanceof User) {
            $this->createAccessDeniedException(APIErrorMessages::FORBIDDEN_ACTION);
        }

        $allOperators = $operatorRepository->findAll();
        $operatorDTOS = array_map(static function ($operator) {
            return OperatorResponseDTOBuilder::buildOperatorResponseDTO($operator);
        }, $allOperators);

        $allTriggers = $triggerTypeRepository->findAll();
        $triggerTypeDTOS = array_map(static function ($trigger) {
            return TriggerTypeResponseBuilder::buildTriggerTypeResponseDTO($trigger);
        }, $allTriggers);


        $getSensorQueryParams = GetSensorQueryDTOBuilder::buildGetSensorQueryDTO(
            limit: 1000,
            groupIDs: $userGroupsFinder->getGroupIDs($user),
        );
        $usersSensors = $sensorRepository->findSensorsByQueryParameters($getSensorQueryParams);
        $sensorsToChooseFrom = [];
        foreach ($usersSensors as $sensor) {
            $sensorsToChooseFrom[] = $sensorResponseDTOBuilder->buildFullSensorResponseDTOWithPermissions($sensor, [RequestTypeEnum::FULL->value]);
        }

        $relaysUserCanTrigger = $relayRepository->findReadingTypeUserHasAccessTo($userGroupsFinder->getGroupIDs($user));
        $relaysUserCanTriggerDTOs = [];
        foreach ($relaysUserCanTrigger as $relay) {
            $relaysUserCanTriggerDTOs[] = $relayResponseDTOBuilder->buildSensorReadingTypeResponseDTO($relay);
        }

        $triggerFormEncapsulationDTO = TriggerFormEncapsulationDTOBuilder::buildTriggerFormEncapsulationDTO(
            $operatorDTOS,
            $triggerTypeDTOS,
            $relaysUserCanTriggerDTOs,
            $sensorsToChooseFrom,
        );

        try {
            $normalizedResponse = $this->normalize($triggerFormEncapsulationDTO);
        } catch (ExceptionInterface) {
            return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_PREPARE_DATA]);
        }

        return $this->sendSuccessfulJsonResponse($normalizedResponse);
    }
}
