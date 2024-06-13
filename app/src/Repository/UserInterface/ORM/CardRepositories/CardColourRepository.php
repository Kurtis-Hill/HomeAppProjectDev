<?php

namespace App\Repository\UserInterface\ORM\CardRepositories;

use App\Entity\UserInterface\Card\Colour;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @extends ServiceEntityRepository<\App\Entity\UserInterface\Card\Colour>
 *
 * @method Colour|null find($id, $lockMode = null, $lockVersion = null)
 * @method Colour|null findOneBy(array $criteria, array $orderBy = null)
 * @method Colour[]    findAll()
 * @method Colour[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CardColourRepository extends ServiceEntityRepository implements CardColourRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Colour::class);
    }

    public function findOneById(int $id): ?Colour
    {
        return $this->find($id);
    }

    public function persist(Colour $cardColour): void
    {
        $this->getEntityManager()->persist($cardColour);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function getFirstColourID(): Colour
    {
        return $this->createQueryBuilder('cardColour')
            ->select()
            ->orderBy('cardColour.colourID', 'ASC')
            ->setMaxResults(1)
            ->getQuery()->getSingleResult();
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

    #[ArrayShape([Colour::class])]
    public function getAllColourObjects(): array
    {
        $qb = $this->createQueryBuilder('c')
            ->orderBy('c.colour', 'ASC');

        return $qb->getQuery()->getResult();
    }
}
