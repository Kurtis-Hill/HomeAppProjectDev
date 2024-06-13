<?php

namespace App\Builders\User\RoomDTOBuilder;

use App\DTOs\User\Internal\RoomDTOs\AddNewRoomDTO;
use App\Entity\User\Room;

class NewRoomInternalDTOBuilder
{
    public static function buildInternalNewRoomDTO(
        string $roomName,
    ): AddNewRoomDTO {
        $room = new Room();

        return new AddNewRoomDTO(
            $roomName,
            $room
        );
    }
}
