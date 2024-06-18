<?php

namespace App\Repository\User\ORM;

use App\Entity\User\Room;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\Persistence\ManagerRegistry;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Deprecated;

/**
 * @extends ServiceEntityRepository<RoomRepository>
 *
 * @method Room|null find($id, $lockMode = null, $lockVersion = null)
 * @method Room|null findOneBy(array $criteria, array $orderBy = null)
 * @method Room[]    findAll()
 * @method Room[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RoomRepository extends ServiceEntityRepository implements RoomRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Room::class);
    }

    public function findRoomByName(string $roomName): ?Room
    {
        $qb = $this->createQueryBuilder('room');
        $expr = $qb->expr();

        $qb->select('room')
            ->where(
                $expr->eq('room.room', ':roomName'),
            );
        $qb->setParameters([
            'roomName' => $roomName,
        ]);

        return $qb->getQuery()->getOneOrNullResult();
    }

    #[Deprecated(reason: 'Use findRoomByName instead', replacement: 'findRoomByName')]
    public function getAllUserRoomsByGroupId(array $groupIDs, int $hydrationMethod = AbstractQuery::HYDRATE_ARRAY): array
    {
        $qb = $this->createQueryBuilder('r');

        $qb->select('r');

        return $qb->getQuery()->getResult($hydrationMethod);
    }

    public function findOneById(int $id): ?Room
    {
        return $this->find($id);
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

    #[Deprecated(reason: 'Use findRoomByName instead', replacement: 'findRoomByName')]
    public function findOneByRoomNameAndGroupId(int $groupID, string $roomName): ?Room
    {
        $qb = $this->createQueryBuilder('room');
        $expr = $qb->expr();

        $qb->select('room')
            ->where(
                $expr->eq('room.room', ':roomName'),
                $expr->eq('room.groupID', ':groupID')
            );
        $qb->setParameters([
            'roomName' => $roomName,
            'groupID' => $groupID
        ]);

        return $qb->getQuery()->getOneOrNullResult();
    }

    #[ArrayShape([Room::class])]
    public function findAllRoomsPaginatedResult(int $offset, int $limit): array
    {
        $qb = $this->createQueryBuilder('room');
        $expr = $qb->expr();

        $qb->select('room')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->orderBy('room.room', 'ASC');

        return $qb->getQuery()->getResult();
    }
}
