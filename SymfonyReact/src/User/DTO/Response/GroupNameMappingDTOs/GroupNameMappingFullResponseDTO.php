<?php

namespace App\User\DTO\Response\GroupNameMappingDTOs;

use App\User\DTO\Response\GroupDTOs\GroupResponseDTO;
use App\User\DTO\Response\UserDTOs\UserFullResponseDTO;
use JetBrains\PhpStorm\Immutable;

#[Immutable]
readonly class GroupNameMappingFullResponseDTO
{
    public function __construct(
        private int $groupMappingID,
        private UserFullResponseDTO $user,
        private GroupResponseDTO $group,
    ) {
    }

    public function getGroupMappingID(): int
    {
        return $this->groupMappingID;
    }

    public function getUser(): UserFullResponseDTO
    {
        return $this->user;
    }

    public function getGroup(): GroupResponseDTO
    {
        return $this->group;
    }
}
