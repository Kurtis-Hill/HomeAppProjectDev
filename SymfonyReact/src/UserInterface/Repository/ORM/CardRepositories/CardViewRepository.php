<?php

namespace App\UserInterface\Repository\ORM\CardRepositories;

use App\User\Entity\User;
use App\UserInterface\Entity\Card\Cardstate;
use App\UserInterface\Entity\Card\CardView;
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

    public function getAllIndexCardDataForUser(User $user, array $filters): array
    {
        $qb = $this->createQueryBuilder('cv');
        $expr = $qb->expr();

        $cardViewStateOne = Cardstate::ON;
        $cardViewStateTwo = Cardstate::INDEX_ONLY;


    }
}
