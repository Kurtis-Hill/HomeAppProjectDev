<?php

namespace App\UserInterface\Repository\ORM\CardRepositories;

use App\Devices\Entity\Devices;
use App\ESPDeviceSensor\Entity\Sensor;
use App\ESPDeviceSensor\Entity\SensorType;
use App\User\Entity\Room;
use App\User\Entity\User;
use App\UserInterface\DTO\CardDataFiltersDTO\CardViewTypeFilterDTO;
use App\UserInterface\DTO\CardDataQueryDTO\CardDataQueryEncapsulationFilterDTO;
use App\UserInterface\DTO\CardDataQueryDTO\CardSensorTypeJoinQueryDTO;
use App\UserInterface\Entity\Card\CardColour;
use App\UserInterface\Entity\Card\Cardstate;
use App\UserInterface\Entity\Card\CardView;
use App\UserInterface\Entity\Icons;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use JetBrains\PhpStorm\Pure;

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

    public function getAllCardSensorDataScalar(User $user, CardDataQueryEncapsulationFilterDTO $cardDataPostFilterDTO, CardViewTypeFilterDTO $cardViewTypeFilterDTO = null): array
    {
        $groupNameIDs = $user->getGroupNameIds();

        $cardViewTwo = Cardstate::DEVICE_ONLY;

        $qb = $this->createQueryBuilder('cv');
        $expr = $qb->expr();
        $sensorTypeAlias = $this->prepareSensorTypesForQuery($cardDataPostFilterDTO, $qb);

        $qb->select($sensorTypeAlias, 'cv', 'cc', 'i', 'sensors', SensorType::ALIAS, Sensor::ALIAS, )
            ->innerJoin(Devices::class, 'devices', Join::WITH, Sensor::ALIAS.'.deviceNameID = devices.deviceNameID')
            ->innerJoin(Cardstate::class, 'cardState', Join::WITH, 'cv.cardStateID = cardState.cardStateID')
            ->innerJoin(CardColour::class, 'cc', Join::WITH, 'cc.colourID = cv.cardColourID')
            ->innerJoin(Icons::class, 'i', Join::WITH, 'cv.cardIconID = i.iconID')
            ->innerJoin(Room::class, 'r', Join::WITH, 'devices.roomID = r.roomID')
            ->innerJoin(SensorType::class, SensorType::ALIAS, Join::WITH, SensorType::ALIAS . $this->createJoinConditionString('sensorTypeID', Sensor::ALIAS));

        $qb->where(
            $expr->orX(
                $expr->eq('cardState.state', ':cardViewOne'),
                $expr->eq('cardState.state', ':cardViewTwo')
            ),
            $expr->eq('cv.userID', ':userID'),
            $expr->in('devices.groupNameID', ':groupNameID'),
        );

        $parameters = [
            'userID' => $user,
            'groupNameID' => $groupNameIDs,
            'cardViewOne' => Cardstate::ON,
            'cardViewTwo' => $cardViewTwo
        ];

        if ($cardViewTypeFilterDTO !== null) {
            if ($cardViewTypeFilterDTO->getDevice() !== null) {
                $qb->andWhere($expr->eq('devices.deviceNameID', ':deviceNameID'));
                $parameters['deviceNameID'] = $cardViewTypeFilterDTO->getDevice()->getDeviceNameID();
            }
            if ($cardViewTypeFilterDTO->getRoom() !== null) {
                $qb->andWhere($expr->eq('room.roomID', ':roomID'));
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

    private function prepareSensorTypesForQuery(CardDataQueryEncapsulationFilterDTO $cardDataFilterDTO, QueryBuilder $qb): string
    {
        $qb->innerJoin(Sensor::class, Sensor::ALIAS, Join::WITH, Sensor::ALIAS.'.sensorNameID = cv.sensorNameID');

        $sensorAlias = [];
        foreach ($cardDataFilterDTO->getSensorTypesToQuery() as $cardSensorTypeQueryDTO) {
            /**@var CardSensorTypeJoinQueryDTO $cardSensorTypeQueryDTO  */
            $sensorNameJoinConditionString = $this->createJoinConditionString(
                $cardSensorTypeQueryDTO->getJoinConditionId(),
                $cardSensorTypeQueryDTO->getJoinConditionColumn()
            );

            $sensorAlias[] = $cardSensorTypeQueryDTO->getAlias();
            $qb->leftJoin($cardSensorTypeQueryDTO->getObject(), $cardSensorTypeQueryDTO->getAlias(), Join::WITH, $cardSensorTypeQueryDTO->getAlias().$sensorNameJoinConditionString);
        }

        return implode(', ', $sensorAlias);
    }

    #[Pure]
    private function createJoinConditionString(string $joinConditionId, string $joinConditionColumn): string
    {
        return sprintf(
            '.%s = %s.%s',
            $joinConditionId,
            $joinConditionColumn,
            $joinConditionId
        );
    }
}
