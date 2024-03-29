<?php

namespace App\Sensors\Voters;

use App\Devices\Entity\Devices;
use App\Sensors\Builders\SensorUpdateBuilders\SensorUpdateDTOBuilder;
use App\Sensors\DTO\Internal\Sensor\NewSensorDTO;
use App\Sensors\DTO\Internal\Sensor\UpdateSensorDTO;
use App\Sensors\Entity\Sensor;
use App\User\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class SensorVoter extends Voter
{
    public const ADD_NEW_SENSOR = 'add-new-sensor';

    public const UPDATE_SENSOR_READING_BOUNDARY = 'update-sensor-boundary-reading';

    public const DEVICE_UPDATE_SENSOR_CURRENT_READING = 'update-sensor-current-reading';

    public const USER_UPDATE_SENSOR_CURRENT_READING = 'update-sensor-current-reading-user';

    public const DELETE_SENSOR = 'delete-sensor';

    public const UPDATE_SENSOR = 'update-sensor';

    public const GET_SENSOR = 'get-single-sensor';

    /**
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [
            self::ADD_NEW_SENSOR,
            self::UPDATE_SENSOR_READING_BOUNDARY,
            self::DEVICE_UPDATE_SENSOR_CURRENT_READING,
            self::DELETE_SENSOR,
            self::UPDATE_SENSOR,
            self::GET_SENSOR,
            self::USER_UPDATE_SENSOR_CURRENT_READING,
        ])) {
            return false;
        }

        return true;
    }

    /**
     * @param string $attribute
     * @param mixed $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        return match ($attribute) {
            self::ADD_NEW_SENSOR => $this->canAddNewSensor($user, $subject),
            self::UPDATE_SENSOR_READING_BOUNDARY => $this->canUpdateSensorBoundaryReading($user, $subject),
            self::DEVICE_UPDATE_SENSOR_CURRENT_READING => $this->canUpdateSensorCurrentReading($user),
            self::DELETE_SENSOR => $this->canDeleteSensor($user, $subject),
            self::UPDATE_SENSOR => $this->canUpdateSensor($user, $subject),
            self::GET_SENSOR => $this->canGetSensor($user, $subject),
//            self::USER_UPDATE_SENSOR_CURRENT_READING => $this->userCanUpdateSensor($user, $sensorReadingType),

            default => false
        };
    }

    private function userCanUpdateSensor(UserInterface $user, Sensor $sensor): bool
    {
        if (!$user instanceof User) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if (!in_array($sensor->getDevice()->getGroupObject()->getGroupID(), $user->getAssociatedGroupIDs(), true)) {
            return false;
        }

        return true;
    }

    private function canAddNewSensor(UserInterface $user, NewSensorDTO $newSensorDTO): bool
    {
        if (!$user instanceof User) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if (
            !in_array(
                $newSensorDTO->getDevice()->getGroupObject()->getGroupID(),
                $user->getAssociatedGroupIDs(), true
            )
        ) {
            return false;
        }

        return true;
    }

    private function canUpdateSensorBoundaryReading(UserInterface $user, Sensor $sensor): bool
    {
        $sensorUpdateDTO = SensorUpdateDTOBuilder::buildSensorUpdateDTO(
            $sensor
        );

        return $this->canUpdateSensor($user, $sensorUpdateDTO);
//        if (!$user instanceof User) {
//            return false;
//        }
//
//        if ($user->isAdmin()) {
//            return true;
//        }
//
//        if (!in_array($sensor->getDevice()->getGroupObject()->getGroupID(), $user->getAssociatedGroupIDs(), true)) {
//            return false;
//        }
//
//        return true;
    }

    private function canUpdateSensorCurrentReading(UserInterface $user): bool
    {
         if (!$user instanceof Devices) {
            return false;
        }

         return true;
    }

    private function canDeleteSensor(UserInterface $user, Sensor $sensor): bool
    {
        if (!$user instanceof User) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if (!in_array($sensor->getDevice()->getGroupObject()->getGroupID(), $user->getAssociatedGroupIDs(), true)) {
            return false;
        }

        return true;
    }

    public function canUpdateSensor(UserInterface $user, UpdateSensorDTO $updateSensorDTO): bool
    {
        if (!$user instanceof User) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }
        $sensor = $updateSensorDTO->getSensor();

        if (!in_array(
            $sensor->getDevice()->getGroupObject()->getGroupID(),
            $user->getAssociatedGroupIDs(),
            true
        )) {
            return false;
        }

        if (($updateSensorDTO->getDeviceID() !== null) && !in_array($updateSensorDTO->getDeviceID()->getGroupObject()->getGroupID(), $user->getAssociatedGroupIDs(), true)) {
            return false;
        }

        return true;
    }

    public function canGetSensor(UserInterface $user, Sensor $sensor): bool
    {
        if (!$user instanceof User) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if (!in_array(
            $sensor->getDevice()->getGroupObject()->getGroupID(),
            $user->getAssociatedGroupIDs(),
            true
        )) {
            return false;
        }

        return true;
    }
}
