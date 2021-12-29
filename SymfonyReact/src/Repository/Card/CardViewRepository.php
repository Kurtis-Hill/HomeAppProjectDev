<?php


namespace App\Repository\Card;

use App\Devices\Entity\Devices;
use App\ESPDeviceSensor\Entity\Sensor;
use App\User\Entity\User;
use App\UserInterface\Entity\Card\Cardstate;
use App\UserInterface\Entity\Card\CardView;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\Expr\Join;
use Symfony\Component\Security\Core\User\UserInterface;
use function Doctrine\ORM\QueryBuilder;

class CardViewRepository extends EntityRepository
{
    private function prepareSensorTypeDataObjectsForQuery(array $sensors, $qb, array $joinCondition): string
    {
        $qb->innerJoin(Sensor::class, 'sensors', Join::WITH, 'sensors.sensorNameID = cv.sensorNameID');

        $joinConditionString = '.' .$joinCondition[1]. ' = ' .$joinCondition[0]. '.' .$joinCondition[1];

        $sensorAlias = [];
        foreach ($sensors as $sensorNames => $sensorData) {
            $sensorAlias[] = $sensorData['alias'];
            $qb->leftJoin($sensorData['object'], $sensorData['alias'], Join::WITH, $sensorData['alias'].$joinConditionString);
        }

        return implode(', ', $sensorAlias);
    }

    //@TODO after DB refactor can use the sensor type array and build scalar results for the card view to reduce queries
    private function prepareSensorTypeDataScalarForQuery(array $sensors, $qb, array $joinCondition): string
    {
        $qb->innerJoin(Sensor::class, 'sensors', Join::WITH, 'sensors.sensorNameID = cv.sensorNameID');

        $joinConditionString = '.' .$joinCondition[1]. ' = ' .$joinCondition[0]. '.' .$joinCondition[1];

        $sensorAlias = [];
        foreach ($sensors as $sensorNames => $sensorData) {
            $sensorAlias[] = $sensorData['alias'];
            $qb->leftJoin($sensorData['object'], $sensorData['alias'], Join::WITH, $sensorData['alias'].$joinConditionString);
        }

        return implode(', ', $sensorAlias);
    }


    public function getAllSensorTypeObjectsForUser(?User $user, array $sensors, string $view): array
    {
        $groupNameIDs = $user->getGroupNameIds();

        $cardViewOne = Cardstate::ON;
        $cardViewTwo = $view;

        $qb = $this->createQueryBuilder('cv');
        $expr = $qb->expr();

        $sensorAlias = $this->prepareSensorTypeDataObjectsForQuery($sensors, $qb, ['sensors', 'sensorNameID']);

        $qb->select($sensorAlias)
            ->innerJoin(Devices::class, 'devices', Join::WITH, 'sensors.deviceNameID = devices.deviceNameID')
            ->innerJoin(Cardstate::class, 'cardState', Join::WITH, 'cv.cardStateID = cardState.cardStateID');

        $qb->where(
            $expr->orX(
                $expr->eq('cardState.state', ':cardviewOne'),
                $expr->eq('cardState.state', ':cardviewTwo')
            ),
            $expr->eq('cv.userID', ':userID'),
            $expr->in('devices.groupNameID', ':groupNameID')
        );

        $qb->setParameters(
            [
                'userID' => $user,
                'groupNameID' => $groupNameIDs,
                'cardviewOne' => $cardViewOne,
                'cardviewTwo' => $cardViewTwo
            ]
        );

        return array_filter($qb->getQuery()->getResult());
    }

    //@TODO ready to implement when DB changes have been made can reduce queries
    public function getAllIndexSensorTypeScalarForUser(?User $user, array $sensors): array
    {
        $groupNameIDs = $user->getGroupNameIds();

        $cardViewOne = Cardstate::ON;
        $cardViewTwo = Cardstate::INDEX_ONLY;

        $qb = $this->createQueryBuilder('cv');
        $expr = $qb->expr();

        $sensorAlias = $this->prepareSensorTypeDataScalarForQuery($sensors, $qb, ['sensors', 'sensorNameID']);

        $qb->select($sensorAlias)
            ->innerJoin(Devices::class, 'devices', Join::WITH, 'sensors.deviceNameID = devices.deviceNameID')
            ->innerJoin(Cardstate::class, 'cardState', Join::WITH, 'cv.cardStateID = cardState.cardStateID');

        //@TODO card state doesnt work needs relating properly
        $qb->where(
            $expr->orX(
                $expr->eq('cardState.state', ':cardviewOne'),
                $expr->eq('cardState.state', ':cardviewTwo')
            ),
            $expr->eq('cv.userID', ':userID'),
            $expr->in('devices.groupNameID', ':groupNameID')
        );

        $qb->setParameters(
            [
                'userID' => $user,
                'groupNameID' => $groupNameIDs,
                'cardviewOne' => $cardViewOne,
                'cardviewTwo' => $cardViewTwo
            ]
        );

        return array_filter($qb->getQuery()->getScalarResult());
    }



    /**
     * @param UserInterface $user
     * @param array $sensors
     * @param integer $deviceDetails
     * @return array
     */
    public function getAllCardReadingsForDevice(UserInterface $user, array $sensors, int $deviceDetails): array
    {
        $groupNameIDs = $user->getGroupNameIds();

        $cardViewOne = Cardstate::ON;
        $cardViewTwo = Cardstate::DEVICE_ONLY;

        $qb = $this->createQueryBuilder('cv');
        $expr = $qb->expr();

        $sensorAlias = $this->prepareSensorTypeDataObjectsForQuery($sensors, $qb, ['sensors', 'sensorNameID']);

        $qb->select($sensorAlias)
            ->innerJoin(Devices::class, 'devices', Join::WITH, 'sensors.deviceNameID = devices.deviceNameID')
            ->innerJoin(Cardstate::class, 'cardState', Join::WITH, 'cv.cardStateID = cardState.cardStateID');

        $qb->where(
            $expr->orX(
                $expr->eq('cardState.state', ':cardviewOne'),
                $expr->eq('cardState.state', ':cardviewTwo')
            ),
            $expr->eq('cv.userID', ':userID'),
            $expr->in('devices.groupNameID', ':groupNameID'),
            $expr->eq('devices.deviceNameID', ':deviceNameID'),
        );

        $qb->setParameters(
            [
                'userID' => $user,
                'groupNameID' => $groupNameIDs,
                'deviceNameID' => $deviceDetails,
                'cardviewOne' => $cardViewOne,
                'cardviewTwo' => $cardViewTwo
            ]
        );

        return array_filter($qb->getQuery()->getResult());
    }

    /**
     * @param array $criteria
     * @param array $sensorData
     * @return mixed
     */
    public function getUsersCurrentlySelectedSensorsCardData(array $criteria, array $sensorData): array
    {
        $qb = $this->createQueryBuilder('cv');

        $sensorAlias = $this->prepareSensorTypeDataObjectsForQuery($sensorData, $qb, ['cv', 'sensorNameID']);

        $qb->select('cv, ' .$sensorAlias)
            ->where(
                $qb->expr()->eq('cv.cardViewID', ':id'),
                $qb->expr()->eq('cv.userID', ':userID')
            )
            ->setParameters(['id' => $criteria['id'], 'userID' => $criteria['userID']]);

        $result = array_filter($qb->getQuery()->getResult());

        $result = array_values($result);

        return $result;
    }

    /**
     * @param int $cardViewID
     * @param int $userID
     * @return CardView|null
     * @throws NonUniqueResultException
     */
    public function findUsersCardFormDataByIdAndUser(int $cardViewID, int $userID): ?CardView
    {
        $qb = $this->createQueryBuilder('cv');
        $expr = $qb->expr();

        $qb->select()
            ->where(
                $expr->eq('cv.cardViewID', ':cardViewID'),
                $expr->eq('cv.userID', ':userID')
            )
            ->setParameters(
                [
                    'cardViewID' => $cardViewID,
                    'userID' => $userID
                ]
            );

        return $qb->getQuery()->getOneOrNullResult();
    }
}
