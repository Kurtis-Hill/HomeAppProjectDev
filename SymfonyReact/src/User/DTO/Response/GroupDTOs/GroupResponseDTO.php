<?php

namespace App\User\DTO\Response\GroupDTOs;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
class GroupResponseDTO
{
    private int $groupID;

    private string $groupName;

    public function __construct(int $groupID, string $groupName)
    {
        $this->groupID = $groupID;
        $this->groupName = $groupName;
    }

//    #[Groups(['full', 'password'])]
    public function getGroupID(): int
    {
        return $this->groupID;
    }

//    #[Groups(['full', 'password'])]
    public function getGroupName(): string
    {
        return $this->groupName;
    }
}
