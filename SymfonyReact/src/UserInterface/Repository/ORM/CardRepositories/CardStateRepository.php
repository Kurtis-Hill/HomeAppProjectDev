<?php

namespace App\UserInterface\Repository\ORM\CardRepositories;

use App\UserInterface\Entity\Card\Cardstate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CardStateRepository extends ServiceEntityRepository implements CardStateRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cardstate::class);
    }

    public function findOneById(int $id)
    {
        return $this->findOneBy(['cardStateID' => $id]);
    }

    public function persist(Cardstate $cardState): void
    {
        $this->getEntityManager()->persist($cardState);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }
}
