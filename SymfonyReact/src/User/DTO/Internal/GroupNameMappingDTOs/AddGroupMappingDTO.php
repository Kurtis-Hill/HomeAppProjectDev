<?php

namespace App\User\DTO\Internal\GroupNameMappingDTOs;

use App\Authentication\Entity\GroupNameMapping;
use App\User\Entity\GroupNames;
use App\User\Entity\User;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
readonly class AddGroupMappingDTO
{
    public function __construct(
        private User $userToAddMappingTo,
        private GroupNames $groupToAddUserTo,
        private GroupNameMapping $newGroupNameMapping,
    ) {
    }

    public function getNewGroupNameMapping(): GroupNameMapping
    {
        return $this->newGroupNameMapping;
    }

    public function getUserToAddMappingTo(): User
    {
        return $this->userToAddMappingTo;
    }

    public function getGroupToAddUserTo(): GroupNames
    {
        return $this->groupToAddUserTo;
    }
}
