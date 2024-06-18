<?php

namespace App\DTOs\User\Internal\GroupDTOs;

use App\Entity\User\Group;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
readonly class UpdateGroupDTO
{
    public function __construct(
        private string $groupName,
        private Group $groupToUpdate,
    ){
    }

    public function getGroupName(): string
    {
        return $this->groupName;
    }

    public function getGroupToUpdate(): Group
    {
        return $this->groupToUpdate;
    }
}
