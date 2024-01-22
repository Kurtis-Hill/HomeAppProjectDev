<?php

namespace App\Sensors\Controller\TriggerControllers;

use App\Common\API\APIErrorMessages;
use App\Common\Builders\Operator\OperatorResponseDTOBuilder;
use App\Common\Repository\OperatorRepository;
use App\Sensors\Builders\GetSensorQueryDTOBuilder\GetSensorQueryDTOBuilder;
use App\Sensors\Builders\SensorResponseDTOBuilders\SensorResponseDTOBuilder;
use App\Sensors\Repository\Sensors\ORM\SensorRepository;
use App\User\Entity\User;
use App\User\Services\GroupServices\UserGroupsFinder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SensorTriggerFormController extends AbstractController
{
    public function getOperatorForm(
        OperatorRepository $operatorRepository,
        SensorRepository $sensorRepository,
        UserGroupsFinder $userGroupsFinder,
        SensorResponseDTOBuilder $sensorResponseDTOBuilder,
    ): JsonResponse {
        $user = $this->getUser();
        if (!$user instanceof User) {
            $this->createAccessDeniedException(APIErrorMessages::FORBIDDEN_ACTION);
        }
        //get all operators
        $allOperators = $operatorRepository->findAll();
        $operatorDTOS = array_map(static function ($operator) {
            return OperatorResponseDTOBuilder::buildOperatorResponseDTO($operator);
        }, $allOperators);

        //get all sensors
        $usersGroups = $userGroupsFinder->getUsersGroups($user);
        $userGroupIDs = array_map(static function ($group) {
            return $group->getGroupID();
        }, $usersGroups);

        $getSensorQueryParams = GetSensorQueryDTOBuilder::buildGetSensorQueryDTO(
            limit: 1000,
            groupIDs: $userGroupIDs,
        );
        $usersSensors = $sensorRepository->findSensorsByQueryParameters($getSensorQueryParams);
        $sensorsToChooseFrom = [];
        foreach ($usersSensors as $sensor) {
            $sensorsToChooseFrom[] = $sensorResponseDTOBuilder->buildSensorResponseDTO($sensor);
        }
//        $sensorResponseDTOs = array_map(static function ($sensor) {
//            return SensorResponseDTOBuilder::buildSensorResponseDTO($sensor);
//        }, $usersSensors);


        // get all bool sensors it could trigger
        // get all triggertypes
        // list monday to friday days
    }

    public function handleFormSubmission(Request $request): JsonResponse
    {
    }
}
