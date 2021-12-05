<?php

namespace App\User\DTO\GroupDTOs;

class GroupNameDTO
{
    private int $groupNameId;

    private string $groupName;

    public function __construct(int $groupNameId, string $groupName)
    {
        $this->groupNameId = $groupNameId;
        $this->groupName = $groupName;
    }

    /**
     * @return int
     */
    public function getGroupNameId(): int
    {
        return $this->groupNameId;
    }

    /**
     * @return string
     */
    public function getGroupName(): string
    {
        return $this->groupName;
    }
}
