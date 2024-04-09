<?php

namespace App\Common\Repository;

use App\Common\Entity\TriggerType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<TriggerType>
 *
 * @method TriggerType|null find($id, $lockMode = null, $lockVersion = null)
 * @method TriggerType|null findOneBy(array $criteria, array $orderBy = null)
 * @method TriggerType[]    findAll()
 * @method TriggerType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TriggerTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TriggerType::class);
    }

    public function persist(TriggerType $triggerType): void
    {
        $this->_em->persist($triggerType);
    }

    public function flush(): void
    {
        $this->_em->flush();
    }
}
