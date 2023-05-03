<?php

namespace App\User\DTO\Response\GroupDTOs;

use App\Common\Services\RequestTypeEnum;
use JetBrains\PhpStorm\Immutable;
use Symfony\Component\Serializer\Annotation\Groups;

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

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getGroupID(): int
    {
        return $this->groupID;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getGroupName(): string
    {
        return $this->groupName;
    }
}
