<?php

namespace App\UserInterface\Repository\ORM;

use App\UserInterface\Entity\Icons;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @extends ServiceEntityRepository<Icons>
 *
 * @method Icons|null find($id, $lockMode = null, $lockVersion = null)
 * @method Icons|null findOneBy(array $criteria, array $orderBy = null)
 * @method Icons[]    findAll()
 * @method Icons[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IconsRepository extends ServiceEntityRepository implements IconsRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Icons::class);
    }

    public function findOneById(int $id): ?Icons
    {
        return $this->find($id);
    }

    public function persist(Icons $cardColour): void
    {
        $this->getEntityManager()->persist($cardColour);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function countAllIcons(): int
    {
        return (int) $this->createQueryBuilder('icons')
            ->select('count(icons.iconID)')
            ->getQuery()->getSingleScalarResult();
    }

    public function getFirstIcon(): Icons
    {
        return $this->createQueryBuilder('icons')
            ->select()
            ->orderBy('icons.iconID', 'ASC')
            ->setMaxResults(1)
            ->getQuery()->getOneOrNullResult();
    }

    #[ArrayShape(['iconID' => 'int', 'iconName' => 'string', 'description' => 'string'])]
    public function getAllIconsAsArray(): array
    {
        $qb = $this->createQueryBuilder('i')
            ->orderBy('i.iconName', 'ASC');

        return $qb->getQuery()->getArrayResult();
    }

    #[ArrayShape([Icons::class])]
    public function getAllIconObjects(): array
    {
        $qb = $this->createQueryBuilder('i')
            ->orderBy('i.iconName', 'ASC');

        return $qb->getQuery()->getResult();
    }
}
