<?php

namespace App\DTOs\User\Internal\GroupMappingDTOs;

use App\Entity\Authentication\GroupMapping;
use App\Entity\User\Group;
use App\Entity\User\User;
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
