<?php

namespace App\User\Voters;

use App\User\DTO\InternalDTOs\GroupDTOs\AddNewGroupDTO;
use App\User\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class GroupVoter extends Voter
{
    public const ADD_NEW_GROUP = 'add-new-group';

    protected function supports(string $attribute, $subject): bool
    {
        if (!in_array($attribute, [self::ADD_NEW_GROUP, self::VIEW_USER_ROOMS], true)) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        return match($attribute) {
            self::ADD_NEW_GROUP => $this->canAddNewGroup($user, $subject),
            default => false,
        };
    }

    private function canAddNewGroup(UserInterface $user, AddNewGroupDTO $groupNames): bool
    {
        if (!$user instanceof User) {
            return false;
        }

        return true;
    }
}
