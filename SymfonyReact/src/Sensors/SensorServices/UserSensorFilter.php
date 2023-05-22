<?php

namespace App\Sensors\SensorServices;

use App\Common\API\APIErrorMessages;
use App\Sensors\DTO\Internal\Sensor\GetSensorQueryDTO;
use App\Sensors\Entity\Sensor;
use App\Sensors\Voters\SensorVoter;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Bundle\SecurityBundle\Security;

class UserSensorFilter
{
    private Security $security;

    public function __construct(
        Security $security,
    ) {
        $this->security = $security;
    }

    /**
     * @param Sensor[] $sensors
     */
    #[ArrayShape(['errors'])]
    public function filterSensorsAllowedForUser(array $sensors, GetSensorQueryDTO $getSensorQueryDTO): array
    {
        $requestedGroupsNameIds = $getSensorQueryDTO->getGroupIDs();
        if (!empty($requestedGroupsNameIds)) {
            foreach ($sensors as $sensor) {
                if (
                    in_array($sensor->getDevice()->getGroupObject()->getGroupID(), $requestedGroupsNameIds, false)
                    && $this->security->isGranted(SensorVoter::GET_SENSOR, $sensor) === false
                ) {
                    $errors[] = sprintf(
                        APIErrorMessages::USER_DOES_NOT_HAVE_ACCESS_TO_REQUESTED,
                        'group',
                        $sensor->getDevice()->getGroupObject()->getGroupID()
                    );
                    $requestedGroupsNameIds = array_filter($requestedGroupsNameIds, static function(int $groupNameID) use ($sensor) {
                        return $groupNameID !== $sensor->getDevice()->getGroupObject()->getGroupID();
                    });
                }
            }
            if (!empty($requestedGroupsNameIds)) {
                foreach ($requestedGroupsNameIds as $requestedGroupsNameId) {
                    if ($this->security->isGranted(SensorVoter::GET_SENSOR, null) === false) {
                        $errors[] = sprintf(
                            APIErrorMessages::USER_DOES_NOT_HAVE_ACCESS_TO_REQUESTED,
                            'group',
                            $requestedGroupsNameId
                        );
                    }
                }
            }
        }

        $requestedDeviceNames = $getSensorQueryDTO->getDeviceNames();
        if (!empty($requestedDeviceNames)) {
            foreach ($sensors as $sensor) {
                if (
                    in_array($sensor->getDevice()->getDeviceName(), $requestedDeviceNames, false)
                    && $this->security->isGranted(SensorVoter::GET_SENSOR, $sensor) === false
                ) {
                    $errors[] = sprintf(
                        APIErrorMessages::USER_DOES_NOT_HAVE_ACCESS_TO_REQUESTED,
                        'device',
                        $sensor->getDevice()->getDeviceName()
                    );
                }
                $requestedDeviceNames = array_filter($requestedDeviceNames, static function(string $requestedDeviceName) use ($sensor) {
                    return $requestedDeviceName !== $sensor->getDevice()->getDeviceName();
                });
            }
            if (!empty($requestedDeviceNames)) {
                foreach ($requestedDeviceNames as $requestedDeviceName) {
                    if ($this->security->isGranted(SensorVoter::GET_SENSOR, null) === false) {
                        $errors[] = sprintf(
                            APIErrorMessages::USER_DOES_NOT_HAVE_ACCESS_TO_REQUESTED,
                            'device',
                            $requestedDeviceName
                        );
                    }
                }
            }
        }

        $requestedDeviceIDs = $getSensorQueryDTO->getDeviceIDs();
        if (!empty($requestedDeviceIDs)) {
            foreach ($sensors as $sensor) {
                if (
                    in_array($sensor->getDevice()->getDeviceID(), $requestedDeviceIDs, false)
                    && $this->security->isGranted(SensorVoter::GET_SENSOR, $sensor) === false
                ) {
                    $errors[] = sprintf(
                        APIErrorMessages::USER_DOES_NOT_HAVE_ACCESS_TO_REQUESTED,
                        'device',
                        $sensor->getDevice()->getDeviceID()
                    );
                }
                $requestedDeviceIDs = array_filter($requestedDeviceIDs, static function(int $requestedDeviceID) use ($sensor) {
                    return $requestedDeviceID !== $sensor->getDevice()->getDeviceID();
                });
            }
            if (!empty($requestedDeviceIDs)) {
                foreach ($requestedDeviceIDs as $requestedDeviceID) {
                    if ($this->security->isGranted(SensorVoter::GET_SENSOR, null) === false) {
                        $errors[] = sprintf(
                            APIErrorMessages::USER_DOES_NOT_HAVE_ACCESS_TO_REQUESTED,
                            'device',
                            $requestedDeviceID
                        );
                    }
                }
            }
        }

        return $errors ?? [];
    }
}
