<?php


namespace App\Sensors\Voters;


use App\Devices\Entity\Devices;
use App\Sensors\DTO\Internal\Sensor\NewSensorDTO;
use App\User\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class SensorVoter extends Voter
{
    public const ADD_NEW_SENSOR = 'add-new-sensor';

    public const UPDATE_SENSOR_READING_BOUNDARY = 'update-sensor-boundary-reading';

    public const UPDATE_SENSOR_CURRENT_READING = 'update-sensor-current-reading';

    /**
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::ADD_NEW_SENSOR, self::UPDATE_SENSOR_READING_BOUNDARY, self::UPDATE_SENSOR_CURRENT_READING])) {
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
                $newSensorDTO->getDevice()->getGroupNameObject()->getGroupNameID(),
                $user->getAssociatedGroupNameIds(), true
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

        if (!in_array($devices->getGroupNameObject()->getGroupNameID(), $user->getAssociatedGroupNameIds(), true)) {
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
}
