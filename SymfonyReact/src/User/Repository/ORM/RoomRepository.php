<?php

namespace App\User\Repository\ORM;

use App\User\Entity\Room;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RoomRepository extends ServiceEntityRepository implements RoomRepositoryInterface
{
    private ManagerRegistry $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
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

    public function persist(Room $room): void
    {
        $this->registry->getManager()->persist($room);
    }

    public function flush(): void
    {
        $this->registry->getManager()->flush();
    }
}
