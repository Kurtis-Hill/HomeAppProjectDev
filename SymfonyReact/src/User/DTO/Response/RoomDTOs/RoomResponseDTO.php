<?php

namespace App\User\DTO\Response\RoomDTOs;

use JetBrains\PhpStorm\Immutable;

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

    public function getRoomID(): int
    {
        return $this->roomID;
    }

    public function getRoomName(): string
    {
        return $this->roomName;
    }
}
