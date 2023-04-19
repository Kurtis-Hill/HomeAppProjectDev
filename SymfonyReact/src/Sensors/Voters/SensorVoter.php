<?php


namespace App\Sensors\Voters;


use App\Devices\Entity\Devices;
use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use App\Sensors\DTO\Internal\Sensor\NewSensorDTO;
use App\Sensors\DTO\Internal\Sensor\UpdateSensorDTO;
use App\Sensors\Entity\Sensor;
use App\User\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class SensorVoter extends Voter
{
    private DeviceRepositoryInterface $deviceRepository;

    public function __construct(DeviceRepositoryInterface $deviceRepository)
    {
        $this->deviceRepository = $deviceRepository;
    }

    public const ADD_NEW_SENSOR = 'add-new-sensor';

    public const UPDATE_SENSOR_READING_BOUNDARY = 'update-sensor-boundary-reading';

    public const UPDATE_SENSOR_CURRENT_READING = 'update-sensor-current-reading';

    public const DELETE_SENSOR = 'delete-sensor';

    public const UPDATE_SENSOR = 'update-sensor';

    public const GET_SINGLE_SENSOR = 'get-single-sensor';

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
            self::UPDATE_SENSOR_CURRENT_READING,
            self::DELETE_SENSOR,
            self::UPDATE_SENSOR,
            self::GET_SINGLE_SENSOR
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
            self::UPDATE_SENSOR_CURRENT_READING => $this->canUpdateSensorCurrentReading($user),
            self::DELETE_SENSOR => $this->canDeleteSensor($user, $subject),
            self::UPDATE_SENSOR => $this->canUpdateSensor($user, $subject),
            self::GET_SINGLE_SENSOR => $this->canGetSingleSensor($user, $subject),
            default => false
        };
    }

    private function canAddNewSensor(UserInterface $user, NewSensorDTO $newSensorDTO): bool
    {
        if (!$user instanceof User) {
            return false;
        }

        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
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

    private function canUpdateSensorBoundaryReading(UserInterface $user, Devices $devices): bool
    {
        if (!$user instanceof User) {
            return false;
        }

        if (!in_array($devices->getGroupObject()->getGroupID(), $user->getAssociatedGroupIDs(), true)) {
            return false;
        }

        return true;
    }

    private function canUpdateSensorCurrentReading(UserInterface $user): bool
    {
         if (!$user instanceof Devices) {
            return false;
        }

         $user->getDeviceID();

         return true;
    }

    private function canDeleteSensor(UserInterface $user, Sensor $sensor): bool
    {
        if (!$user instanceof User) {
            return false;
        }

        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
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

        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
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

        if (!in_array($updateSensorDTO->getDeviceID()?->getGroupObject()->getGroupID(), $user->getAssociatedGroupIDs(), true)) {
            return false;
        }

        return true;
    }

    public function canGetSingleSensor(UserInterface $user, Sensor $sensor): bool
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
