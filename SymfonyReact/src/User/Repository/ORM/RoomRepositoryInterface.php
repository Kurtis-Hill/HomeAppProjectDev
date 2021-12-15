<?php

namespace App\User\Repository\ORM;

use App\User\Entity\Room;
use JetBrains\PhpStorm\NoReturn;

interface RoomRepositoryInterface
{
    public function findDuplicateRoom(string $roomName, int $groupNameId): ?Room;

    public function persist(Room $room): void;

    public function flush(): void;

    public function remove(Room $room): void;

    public function findOneById(int $id): ?Room;
}
