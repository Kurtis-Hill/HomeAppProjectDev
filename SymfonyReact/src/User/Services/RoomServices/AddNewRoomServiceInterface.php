<?php

namespace App\User\Services\RoomServices;

use App\User\DTO\RoomDTOs\AddNewRoomDTO;
use App\User\Entity\Room;

interface AddNewRoomServiceInterface
{
    public function processNewRoomRequest(AddNewRoomDTO $addNewRoomDTO): ?Room;
}
