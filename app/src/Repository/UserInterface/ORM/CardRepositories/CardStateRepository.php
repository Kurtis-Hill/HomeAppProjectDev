<?php

namespace App\Repository\UserInterface\ORM\CardRepositories;

use App\Entity\UserInterface\Card\CardState;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @extends ServiceEntityRepository<\App\Entity\UserInterface\Card\CardState>
 *
 * @method CardState|null find($id, $lockMode = null, $lockVersion = null)
 * @method CardState|null findOneBy(array $criteria, array $orderBy = null)
 * @method CardState[]    findAll()
 * @method CardState[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CardStateRepository extends ServiceEntityRepository implements CardStateRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CardState::class);
    }

    public function findOneById(int $id): ?CardState
    {
        return $this->find($id);
    }

    public function findOneByState(string $state): ?CardState
    {
        return $this->findOneBy(['state' => $state]);
    }

    public function persist(CardState $cardState): void
    {
        $this->getEntityManager()->persist($cardState);
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    #[ArrayShape([CardState::class])]
    public function getAllStatesAsArray(): array
    {
        $qb = $this->createQueryBuilder('cs')
            ->orderBy('cs.stateID', 'ASC');

        return $qb->getQuery()->getArrayResult();
    }

    #[ArrayShape([CardState::class])]
    public function getAllStateAsObjects(): array
    {
        $qb = $this->createQueryBuilder('cs')
            ->orderBy('cs.stateID', 'ASC');

        return $qb->getQuery()->getResult();
    }
}
