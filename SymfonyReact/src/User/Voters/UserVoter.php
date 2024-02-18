<?php

namespace App\User\Voters;

use App\User\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{
    public const ADD_NEW_USER = 'add_new_user';

    public const DELETE_USER = 'delete_user';

    public const UPDATE_USER = 'update_user';

    public const CAN_UPDATE_USER_ROLES = 'can_update_user_roles';

    public const CAN_UPDATE_USER_GROUPS = 'can_update_user_groups';

    public const CAN_UPDATE_USER_PASSWORD = 'can_update_user_password';

    public const CAN_GET_USER = 'can_get_user';

    protected function supports(string $attribute, $subject): bool
    {
        if (!in_array($attribute, [
            self::ADD_NEW_USER,
            self::DELETE_USER,
            self::UPDATE_USER,
            self::CAN_UPDATE_USER_ROLES,
            self::CAN_UPDATE_USER_GROUPS,
            self::CAN_UPDATE_USER_PASSWORD,
            self::CAN_GET_USER,
            ])) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        return match ($attribute) {
            self::ADD_NEW_USER => $this->canAddNewUser($user),
            self::DELETE_USER => $this->canDeleteUser($user),
            self::UPDATE_USER => $this->canUpdateUser($user, $subject),
            self::CAN_UPDATE_USER_ROLES => $this->canUpdateUserRoles($user),
            self::CAN_UPDATE_USER_GROUPS => $this->canUpdateUserGroups($user),
            self::CAN_UPDATE_USER_PASSWORD => $this->canUpdateUserPassword($user, $subject),
            self::CAN_GET_USER => $this->canGetUser($user, $subject),
            default => false,
        };
    }

    private function canGetUser(User $currentUser, User $requestedToView): bool
    {
        return $currentUser->isAdmin() || $currentUser->getUserID() === $requestedToView->getUserID();
    }

    private function canUpdateUserPassword(User $user, $userToUpdate): bool
    {
        return $user->isAdmin() || $user->getUserID() === $userToUpdate->getUserID();
    }

    private function canUpdateUserGroups(User $user): bool
    {
        return $user->isAdmin();
    }

    private function canUpdateUserRoles(User $user): bool
    {
        return $user->isAdmin();
    }

    private function canUpdateUser(User $userDoingUpdating, User $userToUpdate): bool
    {
        return $userDoingUpdating->isAdmin() || $userToUpdate->getUserID() === $userDoingUpdating->getUserID();
    }

    private function canAddNewUser(User $user): bool
    {
        return $user->isAdmin();
    }

    private function canDeleteUser(User $user): bool
    {
        return $user->isAdmin();
    }
}
