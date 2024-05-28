<?php

namespace App\User\Repository\ORM;

use App\User\Entity\Room;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\ORMInvalidArgumentException;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @method Room|null find($id, $lockMode = null, $lockVersion = null)
 * @method Room|null findOneBy(array $criteria, array $orderBy = null)
 * @method Room[]    findAll()
 * @method Room[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
interface RoomRepositoryInterface
{
    /**
     * @throws ORMException
     */
    public function findRoomByName(string $roomName): ?Room;

    /**
     * @throws ORMException
     */
    public function getAllUserRoomsByGroupId(array $groupIDs, int $hydrationMethod = AbstractQuery::HYDRATE_ARRAY): array;

    /**
     * @throws ORMInvalidArgumentException
     * @throws ORMException
     */
    public function persist(Room $room): void;

    /**
     * @throws OptimisticLockException
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

    public function findOneByRoomNameAndGroupId(int $groupID, string $roomName): ?Room;

    #[ArrayShape([Room::class])]
    public function findAllRoomsPaginatedResult(int $offset, int $limit): array;
}
