<?php

namespace App\User\Builders\RoomDTOBuilder;

use App\User\DTO\Response\RoomDTOs\RoomResponseDTO;
use App\User\Entity\Room;

class RoomResponseDTOBuilder
{
    public static function buildRoomResponseDTO(Room $room): RoomResponseDTO
    {
        return new RoomResponseDTO(
            $room->getRoomID(),
            $room->getRoom(),
        );
    }
}
