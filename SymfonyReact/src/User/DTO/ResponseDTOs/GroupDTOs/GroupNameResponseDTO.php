<?php

namespace App\User\DTO\ResponseDTOs\GroupDTOs;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
class GroupNameResponseDTO
{
    private int $groupNameId;

    private string $groupName;

    public function __construct(int $groupNameId, string $groupName)
    {
        $this->groupNameId = $groupNameId;
        $this->groupName = $groupName;
    }

    public function getGroupNameId(): int
    {
        return $this->groupNameId;
    }

    public function getGroupName(): string
    {
        return $this->groupName;
    }
}
