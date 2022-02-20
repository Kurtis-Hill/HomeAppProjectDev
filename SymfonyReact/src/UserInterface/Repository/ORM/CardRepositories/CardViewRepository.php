<?php

namespace App\UserInterface\Repository\ORM\CardRepositories;

use App\Common\Traits\QueryJoinBuilderTrait;
use App\Devices\Entity\Devices;
use App\ESPDeviceSensor\Entity\Sensor;
use App\ESPDeviceSensor\Entity\SensorType;
use App\User\Entity\Room;
use App\User\Entity\User;
use App\UserInterface\DTO\CardDataFiltersDTO\CardViewTypeFilterDTO;
use App\UserInterface\DTO\CardDataQueryDTO\CardDataQueryEncapsulationFilterDTO;
use App\UserInterface\Entity\Card\CardColour;
use App\UserInterface\Entity\Card\Cardstate;
use App\UserInterface\Entity\Card\CardView;
use App\UserInterface\Entity\Icons;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

class CardViewRepository extends ServiceEntityRepository implements CardViewRepositoryInterface
{
    use QueryJoinBuilderTrait;

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

    public function getAllCardSensorDataScalar(User $user, string $cardViewTwo, CardDataQueryEncapsulationFilterDTO $cardDataPostFilterDTO, CardViewTypeFilterDTO $cardViewTypeFilterDTO = null): array
    {
//        dd('sdf');
        $groupNameIDs = $user->getGroupNameIds();

        $qb = $this->createQueryBuilder(CardView::ALIAS);
        $expr = $qb->expr();

        $qb->innerJoin(Sensor::class, Sensor::ALIAS, Join::WITH, Sensor::ALIAS. $this->createJoinConditionString('sensorNameID', CardView::ALIAS));

        $readingTypeAlias = $this->prepareSensorJoinsForQuery($cardDataPostFilterDTO->getReadingTypesToQuery(), $qb);
//dd($readingTypeAlias);
        $qb->select($readingTypeAlias, CardView::ALIAS, Room::ALIAS, CardColour::ALIAS, Icons::ALIAS, 'sensors', Cardstate::ALIAS, Devices::ALIAS, SensorType::ALIAS, Sensor::ALIAS, )
            ->innerJoin(Devices::class, Devices::ALIAS, Join::WITH, Devices::ALIAS . $this->createJoinConditionString('deviceNameID', Sensor::ALIAS))
            ->innerJoin(Cardstate::class, Cardstate::ALIAS, Join::WITH, Cardstate::ALIAS . $this->createJoinConditionString('cardStateID', CardView::ALIAS))
            ->innerJoin(CardColour::class, CardColour::ALIAS, Join::WITH, CardColour::ALIAS .'.colourID = '. CardView::ALIAS . '.cardColourID')
            ->innerJoin(Icons::class, Icons::ALIAS, Join::WITH, Icons::ALIAS . '.iconID = '. CardView::ALIAS. '.cardIconID')
            ->innerJoin(Room::class, Room::ALIAS, Join::WITH, Devices::ALIAS . $this->createJoinConditionString('roomID', Room::ALIAS))
            ->innerJoin(SensorType::class, SensorType::ALIAS, Join::WITH, SensorType::ALIAS . $this->createJoinConditionString('sensorTypeID', Sensor::ALIAS));

        $qb->where(
            $expr->orX(
                $expr->eq(Cardstate::ALIAS . '.state', ':cardViewOne'),
                $expr->eq(Cardstate::ALIAS . '.state', ':cardViewTwo')
            ),
            $expr->eq(CardView::ALIAS . '.userID', ':userID'),
            $expr->in(Devices::ALIAS . '.groupNameID', ':groupNameID'),
        );

        $parameters = [
            'userID' => $user,
            'groupNameID' => $groupNameIDs,
            'cardViewOne' => Cardstate::ON,
            'cardViewTwo' => $cardViewTwo
        ];

        if ($cardViewTypeFilterDTO !== null) {
            if ($cardViewTypeFilterDTO->getDevice() !== null) {
                $qb->andWhere($expr->eq(Devices::ALIAS . '.deviceNameID', ':deviceNameID'));
                $parameters['deviceNameID'] = $cardViewTypeFilterDTO->getDevice()->getDeviceNameID();
            }
            if ($cardViewTypeFilterDTO->getRoom() !== null) {
                $qb->andWhere($expr->eq(Room::ALIAS . '.roomID', ':roomID'));
                $parameters['roomID'] = $cardViewTypeFilterDTO->getRoom()->getRoomID();
            }
        }

        foreach ($cardDataPostFilterDTO->getSensorTypesToExclude() as $excludeSensorType) {
            $sensorTypeAlias = $excludeSensorType->getAlias();
            $sensorTypeID = $excludeSensorType->getSensorTypeID();

            $qb->andWhere($expr->neq(SensorType::ALIAS.'.sensorTypeID', ':' . $sensorTypeAlias));
            $parameters[$sensorTypeAlias] = $sensorTypeID;
        }
        $qb->setParameters($parameters);

        return $qb->getQuery()->getScalarResult();
    }

//    private function prepareSensorJoinsForQuery(array $cardDataFilterDTO, QueryBuilder $qb): string
//    {
//        $alias = [];
//        foreach ($cardDataFilterDTO as $cardSensorTypeQueryDTO) {
//            /** @var  $sensorNameJoinConditionString */
//            $sensorNameJoinConditionString = $this->createJoinConditionString(
//                $cardSensorTypeQueryDTO->getJoinConditionId(),
//                $cardSensorTypeQueryDTO->getJoinConditionColumn()
//            );
//
//            $alias[] = $cardSensorTypeQueryDTO->getAlias();
//            $qb->leftJoin($cardSensorTypeQueryDTO->getObject(), $cardSensorTypeQueryDTO->getAlias(), Join::WITH, $cardSensorTypeQueryDTO->getAlias().$sensorNameJoinConditionString);
//        }
//
//        return implode(', ', $alias);
//    }
//
//    #[Pure]
//    private function createJoinConditionString(string $joinConditionId, string $joinConditionColumn): string
//    {
//        return sprintf(
//            '.%s = %s.%s',
//            $joinConditionId,
//            $joinConditionColumn,
//            $joinConditionId
//        );
//    }

    public function findOneById(int $cardViewID): ?CardView
    {
        return $this->findOneBy(['cardViewID' => $cardViewID]);
    }
}
