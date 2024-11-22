<?php

namespace App\Voters;

use App\DTOs\User\Internal\GroupDTOs\AddNewGroupDTO;
use App\DTOs\User\Internal\GroupDTOs\UpdateGroupDTO;
use App\Entity\User\Group;
use App\Entity\User\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class GroupVoter extends Voter
{
    public const ADD_NEW_GROUP = 'add-new-group';

    public const UPDATE_GROUP = 'update-group';

    public const GET_SINGLE_GROUP = 'get-single-group';

    protected function supports(string $attribute, $subject): bool
    {
        if (!in_array(
            $attribute,
            [self::ADD_NEW_GROUP, self::UPDATE_GROUP, self::GET_SINGLE_GROUP],
            true
        )) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        return match($attribute) {
            self::ADD_NEW_GROUP => $this->canAddNewGroup($user, $subject),
            self::UPDATE_GROUP => $this->canUpdateGroup($user, $subject),
            self::GET_SINGLE_GROUP => $this->canGetSingleGroup($user, $subject),
            default => false,
        };
    }

    public function canGetSingleGroup(User $user, Group $group): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        $usersGroups = $user->getAssociatedGroupIDs();

        return in_array($group->getGroupID(), $usersGroups, true);
    }

    private function canAddNewGroup(User $user, AddNewGroupDTO $groupNames): bool
    {
        return true;
    }

    private function canUpdateGroup(User $user, UpdateGroupDTO $addNewGroupDTO): bool
    {
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
