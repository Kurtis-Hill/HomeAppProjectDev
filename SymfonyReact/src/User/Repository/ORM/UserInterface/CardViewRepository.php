<?php

namespace App\User\Repository\ORM\UserInterface;

use App\User\Entity\UserInterface\Card\CardView;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CardViewRepository extends ServiceEntityRepository implements CardViewRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CardView::class);
    }

    public function persist(CardView $cardView): void
    {
        $this->getEntityManager()->persist($cardView);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }
}
