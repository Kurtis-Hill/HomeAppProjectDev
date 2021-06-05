<?php

namespace App\Repository\Core;

use App\Entity\Core\User;
use App\Entity\Devices\Devices;
use App\Entity\Sensors\Sensors;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query\Expr\Join;

/**
 * @method Sensors|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sensors|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sensors[]    findAll()
 * @method Sensors[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SensorsRepository extends EntityRepository
{
    public function checkForDuplicateSensorOnDevice(Sensors $sensorData): ?Sensors
    {
        $qb = $this->createQueryBuilder('sensor');
        $expr = $qb->expr();

        $qb->select('sensor')
            ->innerJoin(Devices::class, 'device', Join::WITH, 'device.deviceNameID = sensor.deviceNameID')
            ->where(
                $expr->eq('sensor.sensorName', ':sensorName'),
                $expr->eq('device.groupNameID', ':groupName'),
            )
            ->setParameters(
                [
                    'sensorName' => $sensorData->getSensorName(),
                    'groupName' => $sensorData->getDeviceNameID()->getGroupNameObject(),
                ]
            );

        return $qb->getQuery()->getResult()[0] ?? null;
//        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findAllSensorsByAssociatedGroups(User $user)
    {
        $qb = $this->createQueryBuilder('sensor');
        $expr = $qb->expr();

        $qb->select('sensor')
            ->innerJoin(Devices::class, 'device', Join::WITH, 'device.deviceNameID = sensor.deviceNameID')
            ->where(
                $expr->in('device.groupNameID', ':groupNameIds')
            )
            ->setParameters(['groupNameIds' => $user->getGroupNameIds()]);

//        dd($qb->getQuery()->getResult(), $user->getGroupNameIds());
    }



}
