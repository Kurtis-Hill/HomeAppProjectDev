<?php

namespace App\User\DTO\Internal\GroupDTOs;

use App\User\Entity\Group;
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
