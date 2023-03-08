<?php

namespace App\User\DTO\InternalDTOs\GroupDTOs;

use App\User\Entity\GroupNames;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
readonly class UpdateGroupDTO
{
    public function __construct(
        private string $groupName,
        private GroupNames $groupToUpdate,
    ){
    }

    public function getGroupName(): string
    {
        return $this->groupName;
    }

    public function getGroupToUpdate(): GroupNames
    {
        return $this->groupToUpdate;
    }
}
