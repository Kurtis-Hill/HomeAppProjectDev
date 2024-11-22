<?php

namespace App\Repository\Sensor;

use App\Entity\Sensor\SensorTrigger;
use App\Entity\Sensor\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Services\Sensor\Trigger\TriggerHelpers\TriggerDateTimeConvertor;
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

    public function findAllSensorTriggersForBaseReadingIDs(array $baseReadingTypeIDs): array
    {
        $qb = $this->createQueryBuilder('st');
        $expr = $qb->expr();

        $qb->where(
            $expr->orX(
                $expr->in('st.baseReadingTypeThatTriggers', ':baseReadingTypeThatTriggers'),
                $expr->in('st.baseReadingTypeToTriggers', ':baseReadingTypeThatTriggers'),
            )
        )->setParameters(
            [
                'baseReadingTypeThatTriggers' => $baseReadingTypeIDs,
            ]
        );

        return $qb->getQuery()->getResult();
    }

    /**
     * @return SensorTrigger[]
     */
    #[ArrayShape([SensorTrigger::class])]
    public function findAllSensorTriggersForDayAndTimeForSensorThatTriggers(
        AllSensorReadingTypeInterface $sensorReadingType,
        ?string $day = null,
        ?string $time = null,
    ): array {
        $currentTime = TriggerDateTimeConvertor::prepareTimes($time);
        $currentDay = TriggerDateTimeConvertor::prepareDays($day);

        $qb = $this->createQueryBuilder('st');
        $expr = $qb->expr();

        $qb->where(
            $expr->eq('st.baseReadingTypeThatTriggers', ':baseReadingTypeThatTriggers'),
            $expr->eq('st.' . $currentDay, ':currentDay'),
            $expr->eq('st.override', ':override'),
            $expr->orX(
                $expr->orX(
                    $expr->isNull('st.startTime'),
                    $expr->isNull('st.endTime'),
                ),
                $expr->orX(
                    $expr->andX(
                        $expr->lte('st.startTime', ':currentTime'),
                        $expr->gte('st.endTime', ':currentTime'),
                    ),
                ),
                $expr->orX(
                    $expr->andX(
                        $expr->gte('st.startTime', ':currentTime'),
                        $expr->lte('st.endTime', ':currentTime'),
                    ),
                ),
            )
        )->setParameters(
            [
                'baseReadingTypeThatTriggers' => $sensorReadingType->getBaseReadingType()->getBaseReadingTypeID(),
                'currentTime' => $currentTime,
                'currentDay' => true,
                'override' => false,
            ]
        );

        return $qb->getQuery()->getResult();
    }

    /**
     * @return SensorTrigger[]
     */
    #[ArrayShape([SensorTrigger::class])]
    public function findAllSensorTriggersForDayAndTime(
        ?string $day = null,
        ?string $time = null,
    ): array {
        $currentTime = TriggerDateTimeConvertor::prepareTimes($time);
        $currentDay = TriggerDateTimeConvertor::prepareDays($day);

        $qb = $this->createQueryBuilder('st');
        $expr = $qb->expr();

        $qb->where(
            $expr->eq('st.' . $currentDay, ':currentDay'),
            $expr->eq('st.override', ':override'),
            $expr->orX(
                $expr->orX(
                    $expr->isNull('st.startTime'),
                    $expr->isNull('st.endTime'),
                ),
                $expr->orX(
                    $expr->andX(
                        $expr->lte('st.startTime', ':currentTime'),
                        $expr->gte('st.endTime', ':currentTime'),
                    ),
                ),
                $expr->orX(
                    $expr->andX(
                        $expr->gte('st.startTime', ':currentTime'),
                        $expr->lte('st.endTime', ':currentTime'),
                    ),
                ),
            )
        )->setParameters(
            [
                'currentTime' => $currentTime,
                'currentDay' => true,
                'override' => false,
            ]
        );

        return $qb->getQuery()->getResult();
    }

    public function remove(SensorTrigger $sensorTrigger): void
    {
        $this->_em->remove($sensorTrigger);
    }
}
