<?php

namespace App\User\Repository\ORM;

use App\User\Entity;
use App\User\DTO\RoomDTOs\AddNewRoomDTO;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class RoomRepository extends ServiceEntityRepository implements RoomRepositoryInterface
{
    private ManagerRegistry $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
        parent::__construct($registry, 'RoomRepository');
    }

    public function findDuplicateRoom(AddNewRoomDTO $addNewRoomDTO): ?Room
    {
        $qb = $this->createQueryBuilder('room');
        $expr = $qb->expr();

        $qb->select('room')
            ->where(
                $expr->eq('room.room', ':roomName'),
                $expr->eq('room.groupNameId', 'groupNameId')
            );
        $qb->setParameters([
            'roomName' => $addNewRoomDTO->getRoomName(),
            'groupNameId' => $addNewRoomDTO->getGroupNameId()
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
