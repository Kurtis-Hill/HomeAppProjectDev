<?php

namespace App\User\Voters;

use App\Authentication\Entity\GroupNameMapping;
use App\User\DTO\InternalDTOs\GroupNameMappingDTOs\AddGroupNameMappingDTO;
use App\User\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class GroupNameMappingVoter extends Voter
{
    public const ADD_NEW_GROUP_NAME_MAPPING = 'add-new-group-name-mapping';

    public const DELETE_GROUP_NAME_MAPPING = 'delete-group-name-mapping';

    protected function supports(string $attribute, mixed $subject): bool
    {
        if (!in_array(
            $attribute,
            [self::ADD_NEW_GROUP_NAME_MAPPING, self::DELETE_GROUP_NAME_MAPPING],
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
            self::ADD_NEW_GROUP_NAME_MAPPING => $this->canAddNewGroupNameMapping($user, $subject),
            self::DELETE_GROUP_NAME_MAPPING => $this->canDeleteGroupNameMapping($user, $subject),
            default => false,
        };
    }

    private function canAddNewGroupNameMapping(User $user, AddGroupNameMappingDTO $addNewGroupNameMappingDTO): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->getGroupNameID()->getGroupNameID() === $addNewGroupNameMappingDTO->getGroupToAddUserTo()->getGroupNameID()) {
            return true;
        }

        return false;
    }

    private function canDeleteGroupNameMapping(User $user, GroupNameMapping $groupNameMapping): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->getGroupNameID()->getGroupNameID() === $groupNameMapping->getGroupName()->getGroupNameID()) {
            return true;
        }

        return false;
    }
}
