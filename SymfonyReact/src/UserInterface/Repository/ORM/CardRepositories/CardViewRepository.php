<?php

namespace App\UserInterface\Repository\ORM\CardRepositories;

use App\Common\Query\Traits\QueryJoinBuilderTrait;
use App\Devices\Entity\Devices;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorType;
use App\User\Entity\Room;
use App\User\Entity\User;
use App\UserInterface\DTO\Internal\CardDataFiltersDTO\CardViewUriFilterDTO;
use App\UserInterface\DTO\Internal\CardDataQueryDTO\CardDataQueryEncapsulationFilterDTO;
use App\UserInterface\DTO\Internal\CardDataQueryDTO\SensorTypeNotJoinQueryDTO;
use App\UserInterface\Entity\Card\CardColour;
use App\UserInterface\Entity\Card\Cardstate;
use App\UserInterface\Entity\Card\CardView;
use App\UserInterface\Entity\Icons;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CardView>
 *
 * @method CardView|null find($id, $lockMode = null, $lockVersion = null)
 * @method CardView|null findOneBy(array $criteria, array $orderBy = null)
 * @method CardView[]    findAll()
 * @method CardView[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
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

    public function getAllCardSensorDataForUser(
        User $user,
        string $cardViewTwo,
        CardDataQueryEncapsulationFilterDTO $cardDataPostFilterDTO,
        ?CardViewUriFilterDTO $cardViewTypeFilterDTO = null,
        int $hydrationMode = AbstractQuery::HYDRATE_SCALAR,
    ): array {
        $qb = $this->createQueryBuilder(CardView::ALIAS);
        $expr = $qb->expr();

        $this->cardViewBuildBasicJoins($qb, $cardDataPostFilterDTO);
        $qb->where(
            $expr->orX(
                $expr->eq(Cardstate::ALIAS . '.state', ':cardViewOne'),
                $expr->eq(Cardstate::ALIAS . '.state', ':cardViewTwo')
            ),
            $expr->eq(CardView::ALIAS . '.userID', ':userID'),
        );

        $parameters = [
            'userID' => $user,
            'cardViewOne' => Cardstate::ON,
            'cardViewTwo' => $cardViewTwo
        ];

        if (!$user->isAdmin()) {
            $groupNameIDs = $user->getAssociatedGroupNameIds();
            $parameters['groupNameID'] = $groupNameIDs;

            $qb->andWhere(
                $expr->in(Devices::ALIAS . '.groupNameID', ':groupNameID'),
            );
        }


        return $this->cardViewFilterExecution(
            $qb,
            $cardViewTypeFilterDTO,
            $cardDataPostFilterDTO,
            $parameters,
            $hydrationMode
        );
    }

//    public function getAllCardSensorDataForAdmin(
//        User $user,
//        string $cardViewTwo,
//        CardDataQueryEncapsulationFilterDTO $cardDataPostFilterDTO,
//        ?CardViewUriFilterDTO $cardViewTypeFilterDTO = null,
//        int $hydrationMode = AbstractQuery::HYDRATE_SCALAR,
//    ): array {
//        $qb = $this->createQueryBuilder(CardView::ALIAS);
//        $expr = $qb->expr();
//
//        $qb->innerJoin(Sensor::class, Sensor::ALIAS, Join::WITH, Sensor::ALIAS. $this->createJoinConditionString('sensorNameID', CardView::ALIAS));
//
//        $this->cardViewBuildBasicJoins($qb, $cardDataPostFilterDTO);
////        $readingTypeAlias = $this->prepareSensorJoinsForQuery($cardDataPostFilterDTO?->getReadingTypesToQuery(), $qb);
////        $qb->select($readingTypeAlias, CardView::ALIAS, Room::ALIAS, CardColour::ALIAS, Icons::ALIAS, Sensor::ALIAS, Cardstate::ALIAS, Devices::ALIAS, SensorType::ALIAS, Sensor::ALIAS)
////            ->innerJoin(Devices::class, Devices::ALIAS, Join::WITH, Devices::ALIAS . $this->createJoinConditionString('deviceNameID', Sensor::ALIAS))
////            ->innerJoin(Cardstate::class, Cardstate::ALIAS, Join::WITH, Cardstate::ALIAS . $this->createJoinConditionString('cardStateID', CardView::ALIAS))
////            ->innerJoin(CardColour::class, CardColour::ALIAS, Join::WITH, CardColour::ALIAS .'.colourID = '. CardView::ALIAS . '.cardColourID')
////            ->innerJoin(Icons::class, Icons::ALIAS, Join::WITH, Icons::ALIAS . '.iconID = '. CardView::ALIAS. '.cardIconID')
////            ->innerJoin(Room::class, Room::ALIAS, Join::WITH, Devices::ALIAS . $this->createJoinConditionString('roomID', Room::ALIAS))
////            ->innerJoin(SensorType::class, SensorType::ALIAS, Join::WITH, SensorType::ALIAS . $this->createJoinConditionString('sensorTypeID', Sensor::ALIAS));
//
//        $qb->where(
//            $expr->orX(
//                $expr->eq(Cardstate::ALIAS . '.state', ':cardViewOne'),
//                $expr->eq(Cardstate::ALIAS . '.state', ':cardViewTwo')
//            ),
//            $expr->eq(CardView::ALIAS . '.userID', ':userID'),
//        );
//
//        $parameters = [
//            'userID' => $user,
//            'cardViewOne' => Cardstate::ON,
//            'cardViewTwo' => $cardViewTwo
//        ];
//
//        return $this->cardViewFilterExecution(
//            $qb,
//            $cardViewTypeFilterDTO,
//            $cardDataPostFilterDTO,
//            $parameters,
//            $hydrationMode
//        );
//    }

    private function cardViewBuildBasicJoins(QueryBuilder $qb, CardDataQueryEncapsulationFilterDTO $cardDataPostFilterDTO): void
    {
        $qb->innerJoin(Sensor::class, Sensor::ALIAS, Join::WITH, Sensor::ALIAS. $this->createJoinConditionString('sensor', CardView::ALIAS));

        $readingTypeAlias = $this->prepareSensorJoinsForQuery($cardDataPostFilterDTO?->getReadingTypesToQuery(), $qb);
        $qb->select($readingTypeAlias, CardView::ALIAS, Room::ALIAS, CardColour::ALIAS, Icons::ALIAS, Sensor::ALIAS, Cardstate::ALIAS, Devices::ALIAS, SensorType::ALIAS, Sensor::ALIAS)
            ->innerJoin(Devices::class, Devices::ALIAS, Join::WITH, Devices::ALIAS . $this->createJoinConditionString('deviceNameID', Sensor::ALIAS))
            ->innerJoin(Cardstate::class, Cardstate::ALIAS, Join::WITH, Cardstate::ALIAS . $this->createJoinConditionString('stateID', CardView::ALIAS))
            ->innerJoin(CardColour::class, CardColour::ALIAS, Join::WITH, CardColour::ALIAS .'.colourID = '. CardView::ALIAS . '.colourID')
            ->innerJoin(Icons::class, Icons::ALIAS, Join::WITH, Icons::ALIAS . '.iconID = '. CardView::ALIAS. '.cardIconID')
            ->innerJoin(Room::class, Room::ALIAS, Join::WITH, Devices::ALIAS . $this->createJoinConditionString('roomID', Room::ALIAS))
            ->innerJoin(SensorType::class, SensorType::ALIAS, Join::WITH, SensorType::ALIAS . $this->createJoinConditionString('sensorTypeID', Sensor::ALIAS));
    }

    /**
     * @param CardViewUriFilterDTO|null $cardViewTypeFilterDTO
     * @param QueryBuilder $qb
     * @param array $parameters
     * @param CardDataQueryEncapsulationFilterDTO|null $cardDataPostFilterDTO
     * @param int $hydrationMode
     * @return float|int|mixed|string Depends on hydration mode
     *
     * @throws ORMException
     */
    private function cardViewFilterExecution(
        QueryBuilder $qb,
        ?CardViewUriFilterDTO $cardViewTypeFilterDTO,
        ?CardDataQueryEncapsulationFilterDTO $cardDataPostFilterDTO,
        array $parameters,
        int $hydrationMode
    ): mixed {
        $expr = $qb->expr();

        if ($cardViewTypeFilterDTO !== null) {
            if ($cardViewTypeFilterDTO->getDevice() !== null) {
                $qb->andWhere($expr->eq(Devices::ALIAS . '.deviceNameID', ':deviceNameID'));
                $parameters['deviceNameID'] = $cardViewTypeFilterDTO->getDevice()->getDeviceID();
            }
            if ($cardViewTypeFilterDTO->getRoom() !== null) {
                $qb->andWhere($expr->eq(Room::ALIAS . '.roomID', ':roomID'));
                $parameters['roomID'] = $cardViewTypeFilterDTO->getRoom()->getRoomID();
            }
        }

        if ($cardDataPostFilterDTO !== null) {
            /** @var SensorTypeNotJoinQueryDTO $excludeSensorType */
            foreach ($cardDataPostFilterDTO->getSensorTypesToExclude() as $excludeSensorType) {
                $sensorTypeAlias = $excludeSensorType->getAlias();
                $sensorTypeID = $excludeSensorType->getSensorTypeID();

                $qb->andWhere($expr->neq(SensorType::ALIAS . '.sensorTypeID', ':' . $sensorTypeAlias));
                $parameters[$sensorTypeAlias] = $sensorTypeID;
            }
        }
        $qb->setParameters($parameters);

        return $qb->getQuery()->getResult($hydrationMode);
    }
}
