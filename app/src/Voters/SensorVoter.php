<?php

namespace App\Voters;

use App\Builders\Sensor\Request\SensorUpdateBuilders\SensorUpdateDTOBuilder;
use App\DTOs\Sensor\Internal\Sensor\NewSensorDTO;
use App\DTOs\Sensor\Internal\Sensor\UpdateSensorDTO;
use App\DTOs\Sensor\Internal\Trigger\CreateNewTriggerDTO;
use App\Entity\Device\Devices;
use App\Entity\Sensor\Sensor;
use App\Entity\Sensor\SensorTrigger;
use App\Entity\User\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class SensorVoter extends Voter
{
    public const ADD_NEW_SENSOR = 'add-new-sensor';

    public const UPDATE_SENSOR_READING_BOUNDARY = 'update-sensor-boundary-reading';

    public const DEVICE_UPDATE_SENSOR_CURRENT_READING = 'update-sensor-current-reading';

    public const DELETE_SENSOR = 'delete-sensor';

    public const UPDATE_SENSOR = 'update-sensor';

    public const GET_SENSOR = 'get-single-sensor';

    public const CAN_CREATE_TRIGGER = 'can-create-trigger';

    public const CAN_DELETE_TRIGGER = 'can-delete-trigger';

    public const CAN_UPDATE_TRIGGER = 'can-update-trigger';

    public const CAN_GET_SENSOR_TRIGGERS = 'can-get-sensor-triggers';

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
            self::CAN_CREATE_TRIGGER,
            self::CAN_DELETE_TRIGGER,
            self::CAN_UPDATE_TRIGGER,
            self::CAN_GET_SENSOR_TRIGGERS,
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
        if (!$user instanceof UserInterface) {
            return false;
        }

        return match ($attribute) {
            self::ADD_NEW_SENSOR => $this->canAddNewSensor($user, $subject),
            self::UPDATE_SENSOR_READING_BOUNDARY => $this->canUpdateSensorBoundaryReading($user, $subject),
            self::DEVICE_UPDATE_SENSOR_CURRENT_READING => $this->canUpdateSensorCurrentReading($user),
            self::DELETE_SENSOR => $this->canDeleteSensor($user, $subject),
            self::UPDATE_SENSOR => $this->canUpdateSensor($user, $subject),
            self::GET_SENSOR => $this->canGetSensor($user, $subject),
            self::CAN_CREATE_TRIGGER => $this->canCreateTriggerForSensor($user, $subject),
            self::CAN_DELETE_TRIGGER => $this->canDeleteTriggerForSensor($user, $subject),
            self::CAN_UPDATE_TRIGGER => $this->canUpdateTriggerForSensor($user, $subject),
            self::CAN_GET_SENSOR_TRIGGERS => $this->canGetSensorTriggers($user, $subject),
            default => false
        };
    }

    private function canGetSensorTriggers(UserInterface $user, SensorTrigger $sensorTrigger): bool
    {
        return $this->canDeleteTriggerForSensor($user, $sensorTrigger);
    }

    public function canUpdateTriggerForSensor(UserInterface $user, SensorTrigger $sensorTrigger): bool
    {
        return $this->canDeleteTriggerForSensor($user, $sensorTrigger);
    }

    private function canDeleteTriggerForSensor(UserInterface $user, SensorTrigger $sensorTrigger): bool
    {
        if (!$user instanceof User) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        $baseReadingTypeThatTriggers = $sensorTrigger->getBaseReadingTypeThatTriggers();
        $baseReadingTypeThatIsTriggered = $sensorTrigger->getBaseReadingTypeToTriggers();

        $baseReadingTypeThatTriggersPasses = null;
        if ($baseReadingTypeThatTriggers !== null) {
            $baseReadingTypeThatTriggersPasses = in_array(
                $baseReadingTypeThatTriggers->getSensor()->getDevice()->getGroupObject()->getGroupID(),
                $user->getAssociatedGroupIDs(),
                true
            );
        }


        $baseReadingTypeThatIsTriggeredPasses = null;
        if ($baseReadingTypeThatIsTriggered !== null) {
            $baseReadingTypeThatIsTriggeredPasses = in_array(
                $baseReadingTypeThatIsTriggered->getSensor()->getDevice()->getGroupObject()->getGroupID(),
                $user->getAssociatedGroupIDs(),
                true
            );
        }

        return !($baseReadingTypeThatTriggersPasses === false || $baseReadingTypeThatIsTriggeredPasses === false);
    }

    private function canCreateTriggerForSensor(UserInterface $user, CreateNewTriggerDTO $createNewTriggerDTO): bool
    {
        if (!$user instanceof User) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if (
            ($createNewTriggerDTO->getBaseReadingTypeThatIsTriggered() !== null)
            && !in_array(
                $createNewTriggerDTO->getBaseReadingTypeThatIsTriggered()->getSensor()->getDevice()->getGroupObject()->getGroupID(),
                $user->getAssociatedGroupIDs(),
                true
            )
        ) {
            return false;
        }

        if (
            ($createNewTriggerDTO->getBaseReadingTypeThatTriggers() !== null)
            && !in_array(
                $createNewTriggerDTO->getBaseReadingTypeThatTriggers()->getSensor()->getDevice()->getGroupObject()->getGroupID(),
                $user->getAssociatedGroupIDs(),
                true
            )
        ) {
            return false;
        }

        return true;
    }

    private function canAddNewSensor(UserInterface $user, Sensor $newSensor): bool
    {
        if (!$user instanceof User) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        if (
            !in_array(
                $newSensor->getDevice()->getGroupObject()->getGroupID(),
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
