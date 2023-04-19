<?php

namespace App\User\Builders\RoomDTOBuilder;

use App\User\DTO\Internal\RoomDTOs\AddNewRoomDTO;
use App\User\Entity\GroupNames;
use App\User\Entity\Room;

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
