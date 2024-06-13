<?php

namespace App\DTOs\User\Response\RoomDTOs;

use App\Services\Request\RequestTypeEnum;
use JetBrains\PhpStorm\Immutable;
use Symfony\Component\Serializer\Annotation\Groups;

#[Immutable]
class RoomResponseDTO
{
    private int $roomID;

    private string $roomName;

    public function __construct(
      int $roomID,
      string $roomName,
    ) {
        $this->roomID = $roomID;
        $this->roomName = $roomName;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getRoomID(): int
    {
        return $this->roomID;
    }

    #[Groups([
        RequestTypeEnum::FULL->value,
        RequestTypeEnum::ONLY->value,
        RequestTypeEnum::SENSITIVE_FULL->value,
        RequestTypeEnum::SENSITIVE_ONLY->value,
    ])]
    public function getRoomName(): string
    {
        return $this->roomName;
    }
}
