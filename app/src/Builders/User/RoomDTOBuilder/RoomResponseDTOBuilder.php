<?php

namespace App\Builders\User\RoomDTOBuilder;

use App\DTOs\User\Response\RoomDTOs\RoomResponseDTO;
use App\Entity\User\Room;

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
