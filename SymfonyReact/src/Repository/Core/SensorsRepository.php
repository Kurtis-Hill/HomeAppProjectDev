<?php

namespace App\Repository\Core;

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
    public function findSensorFromAPIRequest(Devices $device, string $sensorName)
    {

    }

    public function checkForDuplicateSensor(Sensors $sensorData)
    {
        $qb = $this->createQueryBuilder('sensor');
        $expr = $qb->expr();

        $qb->select('sensor')
            ->innerJoin(Devices::class, 'device', Join::WITH, 'device.deviceNameID = sensor.deviceNameID')
            ->where(
                $expr->eq('sensor.sensorName', ':sensorName'),
                $expr->eq('device.groupNameID', ':groupName')
            )
            ->setParameters(
                [
                    'sensorName' => $sensorData->getSensorName(),
                    'groupName' => $sensorData->getDeviceNameID()->getGroupNameObject()
                ]
            );

        return $qb->getQuery()->getOneOrNullResult();
    }


}
