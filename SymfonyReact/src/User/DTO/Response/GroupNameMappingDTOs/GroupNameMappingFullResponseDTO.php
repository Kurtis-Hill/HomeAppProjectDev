<?php

namespace App\User\DTO\Response\GroupNameMappingDTOs;

use App\Common\Services\RequestTypeEnum;
use App\User\DTO\Response\GroupDTOs\GroupResponseDTO;
use App\User\DTO\Response\UserDTOs\UserFullResponseDTO;
use JetBrains\PhpStorm\Immutable;
use Symfony\Component\Serializer\Annotation\Groups;

#[Immutable]
readonly class GroupNameMappingFullResponseDTO
{
    public function __construct(
        private int $groupMappingID,
        private UserFullResponseDTO $user,
        private GroupResponseDTO $group,
    ) {
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getGroupMappingID(): int
    {
        return $this->groupMappingID;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getUser(): UserFullResponseDTO
    {
        return $this->user;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getGroup(): GroupResponseDTO
    {
        return $this->group;
    }
}
