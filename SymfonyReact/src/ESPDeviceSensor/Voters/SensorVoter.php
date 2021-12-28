<?php


namespace App\ESPDeviceSensor\Voters;


use App\ESPDeviceSensor\DTO\Sensor\NewSensorDTO;
use App\User\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class SensorVoter extends Voter
{
    public const ADD_NEW_SENSOR = 'add-new-sensor';

    public const VIEW_DEVICE_CARD_DATA = 'view-device-card-data';

    /**
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::ADD_NEW_SENSOR, self::VIEW_DEVICE_CARD_DATA])) {
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
            self::ADD_NEW_SENSOR => $this->canAddNewDevice($user, $subject),
            self::VIEW_DEVICE_CARD_DATA => $this->viewDeviceCardData($user, $subject),
            default => false
        };
    }

    private function viewDeviceCardData(UserInterface $user, NewSensorDTO $newSensorDTO): bool
    {
        if (!$user instanceof User) {
            return false;
        }

        if (!in_array(
            $newSensorDTO->getDevice()->getGroupNameObject()->getGroupNameID(),
            $user->getGroupNameIds(),
            true)
        ) {
            return false;
        }

        return true;
    }

    private function canAddNewDevice(UserInterface $user, NewSensorDTO $newSensorDTO): bool
    {
        if (!$user instanceof User) {
            return false;
        }

        if (!in_array($newSensorDTO->getDevice()->getGroupNameObject()->getGroupNameID(), $user->getGroupNameIds(), true)) {
            return false;
        }

        return true;
    }
}
