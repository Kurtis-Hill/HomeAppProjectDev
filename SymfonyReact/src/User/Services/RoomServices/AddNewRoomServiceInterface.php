<?php

namespace App\User\Services\RoomServices;

use App\Entity\Core\GroupNames;
use App\User\DTO\RoomDTOs\AddNewRoomDTO;
use App\User\Entity\Room;

interface AddNewRoomServiceInterface
{
    public function processNewRoomRequest(AddNewRoomDTO $addNewRoomDTO): ?Room;

    public function validateAndCreateRoom(AddNewRoomDTO $addNewRoomDTO, GroupNames $groupName): ?Room;

    public function getUserInputErrors(): array;

    public function getServerErrors(): array;
}
