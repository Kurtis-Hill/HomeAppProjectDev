<?php

namespace App\User\DTO\ResponseDTOs\GroupDTOs;

use JetBrains\PhpStorm\Immutable;
use Symfony\Component\Serializer\Annotation\Groups;

#[Immutable]
class GroupNameResponseDTO
{
    private int $groupNameID;

    private string $groupName;

    public function __construct(int $groupNameID, string $groupName)
    {
        $this->groupNameID = $groupNameID;
        $this->groupName = $groupName;
    }

//    #[Groups(['full', 'password'])]
    public function getGroupNameID(): int
    {
        return $this->groupNameID;
    }

//    #[Groups(['full', 'password'])]
    public function getGroupName(): string
    {
        return $this->groupName;
    }
}
