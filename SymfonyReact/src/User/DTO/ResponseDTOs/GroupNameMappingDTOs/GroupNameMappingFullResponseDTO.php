<?php

namespace App\User\DTO\ResponseDTOs\GroupNameMappingDTOs;

use App\User\DTO\ResponseDTOs\GroupDTOs\GroupNameResponseDTO;
use App\User\DTO\ResponseDTOs\UserDTOs\UserFullResponseDTO;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
readonly class GroupNameMappingFullResponseDTO
{
    public function __construct(
        private int $groupNameMappingID,
        private UserFullResponseDTO $user,
        private GroupNameResponseDTO $groupName,
    ) {
    }

    public function getGroupNameMappingID(): int
    {
        return $this->groupNameMappingID;
    }

    public function getUser(): UserFullResponseDTO
    {
        return $this->user;
    }

    public function getGroupName(): GroupNameResponseDTO
    {
        return $this->groupName;
    }
}
