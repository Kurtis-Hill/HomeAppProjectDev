<?php

namespace App\UserInterface\Repository\ORM;

use App\UserInterface\Entity\Icons;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use JetBrains\PhpStorm\ArrayShape;

class IconsRepository extends ServiceEntityRepository implements IconsRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Icons::class);
    }

    public function findOneById(int $id)
    {
        return $this->findOneBy(['iconID' => $id]);
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

    #[ArrayShape([Icons::class])]
    public function getAllIcons(): array
    {
        $qb = $this->createQueryBuilder('i')
            ->orderBy('i.iconName', 'ASC');

        return $qb->getQuery()->getArrayResult();
    }
}
