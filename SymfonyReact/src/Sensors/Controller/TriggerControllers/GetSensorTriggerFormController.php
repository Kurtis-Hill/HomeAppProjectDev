<?php

namespace App\Sensors\Controller\TriggerControllers;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\API\Traits\HomeAppAPITrait;
use App\Common\Builders\Operator\OperatorResponseDTOBuilder;
use App\Common\Repository\OperatorRepository;
use App\Common\Repository\TriggerTypeRepository;
use App\Common\Services\RequestTypeEnum;
use App\Sensors\Builders\GetSensorQueryDTOBuilder\GetSensorQueryDTOBuilder;
use App\Sensors\Builders\SensorReadingTypeResponseBuilders\Bool\RelayResponseDTOBuilder;
use App\Sensors\Builders\SensorResponseDTOBuilders\SensorResponseDTOBuilder;
use App\Sensors\Builders\TriggerResponseBuilder\TriggerTypeResponseBuilder;
use App\Sensors\DTO\Response\TriggerTypeResponse\TriggerFormEncapsulationDTO;
use App\Sensors\Repository\ReadingType\ORM\RelayRepository;
use App\Sensors\Repository\Sensors\ORM\SensorRepository;
use App\User\Entity\User;
use App\User\Services\GroupServices\UserGroupsFinder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
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
        $triggerFormEncapsulationDTO = new TriggerFormEncapsulationDTO(
            $operatorDTOS,
            $triggerTypeDTOS,
            $relaysUserCanTriggerDTOs,
            $sensorsToChooseFrom,
        );

        try {
            $normalizedResponse = $this->normalizeResponse($triggerFormEncapsulationDTO);
        } catch (ExceptionInterface) {
            return $this->sendInternalServerErrorJsonResponse([APIErrorMessages::FAILED_TO_PREPARE_DATA]);
        }

        return $this->sendSuccessfulJsonResponse($normalizedResponse);
    }
}
