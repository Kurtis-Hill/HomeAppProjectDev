<?php

namespace App\User\Repository\ORM;

use App\User\Entity;
use App\User\DTO\RoomDTOs\AddNewRoomDTO;

interface RoomRepositoryInterface
{
    public function findDuplicateRoom(AddNewRoomDTO $addNewRoomDTO): ?Room;

    public function persist(Room $room): void;

    public function flush(): void;
}
