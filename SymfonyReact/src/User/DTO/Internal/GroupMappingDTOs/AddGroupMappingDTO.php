<?php

namespace App\User\DTO\Internal\GroupMappingDTOs;

use App\Authentication\Entity\GroupMapping;
use App\User\Entity\Group;
use App\User\Entity\User;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
readonly class AddGroupMappingDTO
{
    public function __construct(
        private User $userToAddMappingTo,
        private Group $groupToAddUserTo,
        private GroupMapping $newGroupNameMapping,
    ) {
    }

    public function getNewGroupMapping(): GroupMapping
    {
        return $this->newGroupNameMapping;
    }

    public function getUserToAddMappingTo(): User
    {
        return $this->userToAddMappingTo;
    }

    public function getGroupToAddUserTo(): Group
    {
        return $this->groupToAddUserTo;
    }
}
