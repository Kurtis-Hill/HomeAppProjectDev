<?php

namespace App\User\Voters;

use App\User\DTO\Internal\GroupDTOs\AddNewGroupDTO;
use App\User\DTO\Internal\GroupDTOs\UpdateGroupDTO;
use App\User\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class GroupVoter extends Voter
{
    public const ADD_NEW_GROUP = 'add-new-group';

    public const UPDATE_GROUP = 'update-group';

    protected function supports(string $attribute, $subject): bool
    {
        if (!in_array(
            $attribute,
            [self::ADD_NEW_GROUP, self::UPDATE_GROUP],
            true
        )) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        return match($attribute) {
            self::ADD_NEW_GROUP => $this->canAddNewGroup($user, $subject),
            self::UPDATE_GROUP => $this->canUpdateGroup($user, $subject),
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

    private function canUpdateGroup(UserInterface $user, UpdateGroupDTO $addNewGroupDTO): bool
    {
        if (!$user instanceof User) {
            return false;
        }

        if ($user->isAdmin()) {
            return true;
        }

        $groupToUpdate = $addNewGroupDTO->getGroupToUpdate();
        if (!in_array(
            $groupToUpdate->getGroupID(),
            $user->getAssociatedGroupIDs(),
            true
        )) {
            return false;
        }

        return true;
    }
}
