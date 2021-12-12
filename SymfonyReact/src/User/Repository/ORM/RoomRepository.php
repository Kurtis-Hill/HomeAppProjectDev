<?php

namespace App\User\Repository\ORM;

use App\User\Entity\Room;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use JetBrains\PhpStorm\NoReturn;

class RoomRepository extends ServiceEntityRepository implements RoomRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Room::class);
    }

    public function findDuplicateRoom(string $roomName, int $groupNameId): ?Room
    {
        $qb = $this->createQueryBuilder('room');
        $expr = $qb->expr();

        $qb->select('room')
            ->where(
                $expr->eq('room.room', ':roomName'),
                $expr->eq('room.groupNameID', ':groupNameId')
            );
        $qb->setParameters([
            'roomName' => $roomName,
            'groupNameId' => $groupNameId
        ]);

        return $qb->getQuery()->getOneOrNullResult();
    }

    #[NoReturn]
    public function persist(Room $room): void
    {
        $this->getEntityManager()->persist($room);
    }

    #[NoReturn]
    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    #[NoReturn]
    public function remove(Room $room): void
    {
        $this->getEntityManager()->remove($room);
    }
}
