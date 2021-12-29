<?php

namespace App\User\Repository\ORM;

use App\User\Entity\Room;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;

interface RoomRepositoryInterface
{
    /**
     * @throws ORMException
     */
    public function findDuplicateRoom(string $roomName, int $groupNameId): ?Room;

    /**
     * @throws ORMException
     */
    public function getAllUserRoomsByGroupId($groupNameId): array;

    public function persist(Room $room): void;

    /**
     * @throws ORMException
     */
    public function flush(): void;

    /**
     * @throws ORMException
     */
    public function remove(Room $room): void;

    /**
     * @throws ORMException | NonUniqueResultException
     */
    public function findOneById(int $id): ?Room;
}
