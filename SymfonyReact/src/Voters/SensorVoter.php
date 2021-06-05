<?php


namespace App\Voters;


use App\Entity\Core\User;
use App\Entity\Devices\Devices;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class SensorVoter extends Voter
{
    public const ADD_NEW_SENSOR = 'add-new-sensor';

    /**
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::ADD_NEW_SENSOR])) {
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
            default => false
        };
    }

    /**
     * @param UserInterface $user
     * @param Devices $devices
     * @return bool
     */
    private function canAddNewDevice(UserInterface $user, Devices $devices): bool
    {
        if (!$user instanceof User) {
            return false;
        }

        if (!in_array($devices->getGroupNameObject()->getGroupNameID(), $user->getGroupNameIds(), true)) {
            return false;
        }

        return true;
    }
}
