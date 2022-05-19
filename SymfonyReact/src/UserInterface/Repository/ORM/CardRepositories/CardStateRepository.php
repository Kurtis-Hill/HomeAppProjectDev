<?php

namespace App\UserInterface\Repository\ORM\CardRepositories;

use App\UserInterface\Entity\Card\Cardstate;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use JetBrains\PhpStorm\ArrayShape;

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

    public function findOneByState(string $state): ?CardState
    {
        return $this->findOneBy(['state' => $state]);
    }

    public function persist(Cardstate $cardState): void
    {
        $this->getEntityManager()->persist($cardState);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    #[ArrayShape([Cardstate::class])]
    public function getAllStates(): array
    {
        $qb = $this->createQueryBuilder('cs')
            ->orderBy('cs.cardStateID', 'ASC');

        return $qb->getQuery()->getArrayResult();
    }
}
