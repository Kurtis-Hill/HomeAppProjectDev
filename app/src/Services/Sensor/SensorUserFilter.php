<?php

namespace App\Services\Sensor;

use App\DTOs\Sensor\Internal\Sensor\GetSensorQueryDTO;
use App\Entity\Sensor\Sensor;
use App\Entity\User\User;
use App\Exceptions\Sensor\UserNotAllowedException;
use App\Services\API\APIErrorMessages;
use App\Voters\SensorVoter;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Bundle\SecurityBundle\Security;

class SensorUserFilter
{
    private Security $security;

    private array $errors = [];

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * @param Sensor[] $sensors
     * @throws UserNotAllowedException
     */
    #[ArrayShape([Sensor::class])]
    public function filterSensorsAllowedForUser(array $sensors, GetSensorQueryDTO $getSensorQueryDTO): array
    {
        $requestedDeviceNames = $getSensorQueryDTO->getDeviceNames();
        $requestedDeviceIDs = $getSensorQueryDTO->getDeviceIDs();
        $requestedGroupsIDs = $getSensorQueryDTO->getGroupIDs();
        $requestedCardViewIDs = $getSensorQueryDTO->getCardViewIDs();

        $user = $this->security->getUser();
        if (!$user instanceof User) {
            throw new UserNotAllowedException();
        }

        foreach ($sensors as $sensor) {
            if ($this->security->isGranted(SensorVoter::GET_SENSOR, $sensor) === true) {
                $allowedSensors[] = $sensor;
                continue;
            }
            $notAllowedSensors[] = [
                'sensorID' => $sensor->getSensorID(),
                'sensorName' => $sensor->getSensorName(),
                'deviceName' => $sensor->getDevice()->getDeviceName(),
                'deviceID' => $sensor->getDevice()->getDeviceID(),
                'groupID' => $sensor->getDevice()->getGroupObject()->getGroupID(),
            ];
        }

        if (!empty($notAllowedSensors)) {
            $errors = [];
            foreach ($notAllowedSensors as $notAllowedSensor) {
                $notAllowedDeviceID = $notAllowedSensor['deviceID'];
                if (
                    !empty($requestedDeviceIDs)
                    && in_array($notAllowedDeviceID, $requestedDeviceIDs, false)
                ) {
                    $errors[] = sprintf(
                        APIErrorMessages::USER_DOES_NOT_HAVE_ACCESS_TO_REQUESTED,
                        'deviceID',
                        $notAllowedDeviceID
                    );
                }
                if (
                    !empty($requestedDeviceNames)
                    && in_array($notAllowedSensor['deviceName'], $requestedDeviceNames, false)
                ) {
                    $errors[] = sprintf(
                        APIErrorMessages::USER_DOES_NOT_HAVE_ACCESS_TO_REQUESTED,
                        'deviceName',
                        $notAllowedSensor['deviceName']
                    );
                }
                if (
                    !empty($requestedGroupsIDs)
                    && in_array($notAllowedSensor['groupID'], $requestedGroupsIDs, false)
                ) {
                    $errors[] = sprintf(
                        APIErrorMessages::USER_DOES_NOT_HAVE_ACCESS_TO_REQUESTED,
                        'groupID',
                        $notAllowedSensor['groupID']
                    );
                }
                if (
                    !empty($requestedCardViewIDs)
                    && in_array($notAllowedDeviceID, $requestedCardViewIDs, false)
                ) {
                    $errors[] = sprintf(
                        APIErrorMessages::USER_DOES_NOT_HAVE_ACCESS_TO_REQUESTED,
                        'cardViewID',
                        $notAllowedDeviceID
                    );
                }
            }
            $errors = array_unique($errors);
            $this->errors = $errors;
        }

        return $allowedSensors ?? [];
    }

    #[ArrayShape(['errors' => "string"])]
    public function getErrors(): array
    {
        return $this->errors;
    }
}
