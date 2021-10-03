<?php


namespace App\Devices\Voters;

use App\Entity\Core\GroupNames;
use App\Entity\Core\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class DeviceVoter extends Voter
{
    public const ADD_NEW_DEVICE = 'add-new-device';

    /**
     * @param string $attribute
     * @param mixed $subject
     * @return bool
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array($attribute, [self::ADD_NEW_DEVICE])) {
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
          self::ADD_NEW_DEVICE => $this->canAddNewDevice($user, $subject),
          default => false
        };
    }

    /**
     * @param UserInterface $user
     * @param GroupNames|null $groupNameObject
     * @return bool
     */
    private function canAddNewDevice(UserInterface $user, ?GroupNames $groupNameObject): bool
    {
        if (!$user instanceof User) {
            return false;
        }

        $isCallable = [$groupNameObject, 'getGroupNameID'];
        if (!is_callable($isCallable)) {
            return false;
        }

        if (!in_array($groupNameObject?->getGroupNameID(), $user->getGroupNameIds(), true)) {
            return false;
        }

        return true;
    }

}
