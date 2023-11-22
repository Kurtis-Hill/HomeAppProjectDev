<?php

namespace App\Sensors\Repository;

use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTrigger;
use App\Sensors\SensorServices\TriggerHelpers\TriggerDateTimeConvertor;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @extends ServiceEntityRepository<SensorTrigger>
 *
 * @method SensorTrigger|null find($id, $lockMode = null, $lockVersion = null)
 * @method SensorTrigger|null findOneBy(array $criteria, array $orderBy = null)
 * @method SensorTrigger[]    findAll()
 * @method SensorTrigger[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SensorTriggerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SensorTrigger::class);
    }

    public function persist(SensorTrigger $sensorTrigger): void
    {
        $this->_em->persist($sensorTrigger);
    }

    public function flush(): void
    {
        $this->_em->flush();
    }

    /**
     * @return SensorTrigger[]
     */
    #[ArrayShape([SensorTrigger::class])]
    public function findAllSensorTriggersForDayAndTime(
        Sensor $sensor,
        ?string $day = null,
        ?string $time = null,
    ): array {
        $currentTime = TriggerDateTimeConvertor::prepareTimesForComparison($time);
        $currentDay = TriggerDateTimeConvertor::prepareDaysForComparison($day);

        $qb = $this->createQueryBuilder('st');
        $expr = $qb->expr();

        $qb->where(
            $expr->eq('st.sensor', ':sensor'),
            $expr->eq('st.' . $currentDay, true),
            $expr->orX(
                $expr->isNull('st.startTime'),
                $expr->isNull('st.endTime'),
                $expr->andX(
                    $expr->gte('st.startTime', ':currentTime'),
                    $expr->lte('st.endTime', ':currentTime'),
                )
            )
        )->setParameters(
            [
                'sensor', $sensor,
                'currentTime' => $currentTime,
            ]
        );

        return $qb->getQuery()->getResult();
    }
}
