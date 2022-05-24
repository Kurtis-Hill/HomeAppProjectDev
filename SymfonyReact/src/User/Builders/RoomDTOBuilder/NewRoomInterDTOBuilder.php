<?php

namespace App\User\Builders\RoomDTOBuilder;

use App\User\DTO\InternalDTOs\RoomDTOs\AddNewRoomDTO;
use App\User\Entity\GroupNames;

class NewRoomInterDTOBuilder
{
    public static function buildInternalNewRoomDTO(
        string $roomName,
        GroupNames $groupNameID,
    ): AddNewRoomDTO {
        return new AddNewRoomDTO(
            $roomName,
            $groupNameID
        );
    }
}
