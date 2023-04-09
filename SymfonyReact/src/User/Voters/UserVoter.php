<?php

namespace App\User\Voters;

use App\User\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{
    public const ADD_NEW_USER = 'add_new_user';

    public const DELETE_USER = 'delete_user';

    protected function supports(string $attribute, $subject): bool
    {
        if (!in_array($attribute, [
            self::ADD_NEW_USER,
            self::DELETE_USER,
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
            default => false,
        };
    }

    public function canAddNewUser(User $user): bool
    {
        return $user->isAdmin();
    }

    public function canDeleteUser(User $user): bool
    {
        return $user->isAdmin();
    }
}
