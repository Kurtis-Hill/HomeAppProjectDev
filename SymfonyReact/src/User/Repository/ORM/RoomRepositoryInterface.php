<?php

namespace App\User\Repository\ORM;

use App\User\Entity\Room;
use JetBrains\PhpStorm\NoReturn;

interface RoomRepositoryInterface
{
    public function findDuplicateRoom(string $roomName, int $groupNameId): ?Room;

    #[NoReturn]
    public function persist(Room $room): void;

    #[NoReturn]
    public function flush(): void;

    #[NoReturn]
    public function remove(Room $room): void;
}
