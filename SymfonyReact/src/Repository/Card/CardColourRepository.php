<?php

namespace App\Repository\Card;

use App\UserInterface\Entity\Card\CardColour;
use Doctrine\ORM\EntityRepository;

/**
 * @method CardColour|null find($id, $lockMode = null, $lockVersion = null)
 * @method CardColour|null findOneBy(array $criteria, array $orderBy = null)
 * @method CardColour[]    findAll()
 * @method CardColour[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CardColourRepository extends EntityRepository
{
    public function getAllColours(): array
    {
        $qb = $this->createQueryBuilder('c')
              ->orderBy('c.colour', 'ASC');

        return $qb->getQuery()->getArrayResult();
    }

    public function getFirstColourId(): CardColour
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
}
