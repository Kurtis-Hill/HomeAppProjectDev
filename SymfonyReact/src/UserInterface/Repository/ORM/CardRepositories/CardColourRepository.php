<?php

namespace App\UserInterface\Repository\ORM\CardRepositories;

use App\UserInterface\Entity\Card\CardColour;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @extends ServiceEntityRepository<CardColour>
 *
 * @method CardColour|null find($id, $lockMode = null, $lockVersion = null)
 * @method CardColour|null findOneBy(array $criteria, array $orderBy = null)
 * @method CardColour[]    findAll()
 * @method CardColour[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CardColourRepository extends ServiceEntityRepository implements CardColourRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CardColour::class);
    }

    public function findOneById(int $id): ?CardColour
    {
        return $this->find($id);
    }

    public function persist(CardColour $cardColour): void
    {
        $this->getEntityManager()->persist($cardColour);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function getFirstColourID(): CardColour
    {
        return $this->createQueryBuilder('cardColour')
            ->select()
            ->orderBy('cardColour.colourID', 'ASC')
            ->setMaxResults(1)
            ->getQuery()->getOneOrNullResult();
    }

    public function countAllColours(): int
    {
        return (int) $this->createQueryBuilder('cardColour')
            ->select('count(cardColour.colourID)')
            ->getQuery()->getSingleScalarResult();
    }

    #[ArrayShape(['colourID' => "int", 'colour' => "string", 'shade' => "string"])]
    public function getAllColoursAsArray(): array
    {
        $qb = $this->createQueryBuilder('c')
            ->orderBy('c.colour', 'ASC');

        return $qb->getQuery()->getArrayResult();
    }

    #[ArrayShape([CardColour::class])]
    public function getAllColourObjects(): array
    {
        $qb = $this->createQueryBuilder('c')
            ->orderBy('c.colour', 'ASC');

        return $qb->getQuery()->getResult();
    }
}
