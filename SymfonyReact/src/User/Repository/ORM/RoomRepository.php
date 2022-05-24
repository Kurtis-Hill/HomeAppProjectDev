<?php

namespace App\User\Repository\ORM;

use App\User\Entity\Room;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RoomRepository extends ServiceEntityRepository implements RoomRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Room::class);
    }

    public function findDuplicateRoom(string $roomName, int $groupNameID): ?Room
    {
        $qb = $this->createQueryBuilder('room');
        $expr = $qb->expr();

        $qb->select('room')
            ->where(
                $expr->eq('room.room', ':roomName'),
                $expr->eq('room.groupNameID', ':groupNameID')
            );
        $qb->setParameters([
            'roomName' => $roomName,
            'groupNameID' => $groupNameID
        ]);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function getAllUserRoomsByGroupId($groupNameId): array
    {
        $qb = $this->createQueryBuilder('r');

        $qb->select('r.roomID, r.room')
            ->where(
                $qb->expr()->in('r.groupNameID', ':groupNameID')
            )
            ->setParameter('groupNameID', $groupNameId);

        return $qb->getQuery()->getArrayResult();
    }

    public function findOneById(int $id): ?Room
    {
        return $this->findOneBy(['roomID' => $id]);
    }

    public function persist(Room $room): void
    {
        $this->getEntityManager()->persist($room);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function remove(Room $room): void
    {
        $this->getEntityManager()->remove($room);
    }

    public function findOneByRoomNameAndGroupNameId(int $groupNameId, string $roomName): ?Room
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
}
