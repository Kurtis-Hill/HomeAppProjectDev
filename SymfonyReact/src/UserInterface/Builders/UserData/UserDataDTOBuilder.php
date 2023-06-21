<?php

namespace App\UserInterface\Builders\UserData;

use App\User\Builders\GroupName\GroupNameResponseDTOBuilder;
use App\User\Builders\RoomDTOBuilder\RoomResponseDTOBuilder;
use App\UserInterface\DTO\Response\UserData\UserDataResponseDTO;

class UserDataDTOBuilder
{
    public static function buildUserDataDTOBuilder(
        array $userRooms,
        array $userGroups,
    ): UserDataResponseDTO {
        foreach ($userRooms as $userRoom) {
            $userRoomDTOs[] = RoomResponseDTOBuilder::buildRoomResponseDTO(
                $userRoom
            );
        }

        foreach ($userGroups as $userGroup) {
            $userGroupDTOs[] = GroupNameResponseDTOBuilder::buildGroupNameResponseDTO(
                $userGroup
            );
        }

        return new UserDataResponseDTO(
            $userRoomDTOs ?? [],
                $userGroupDTOs ?? [],
        );
    }
}
