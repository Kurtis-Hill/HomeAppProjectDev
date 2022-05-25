<?php

namespace App\User\DTO\ResponseDTOs\GroupDTOs;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
class GroupNameResponseDTO
{
    private int $groupNameID;

    private string $groupName;

    public function __construct(int $groupNameId, string $groupName)
    {
        $this->groupNameID = $groupNameId;
        $this->groupName = $groupName;
    }

    public function getGroupNameID(): int
    {
        return $this->groupNameID;
    }

    public function getGroupName(): string
    {
        return $this->groupName;
    }
}
