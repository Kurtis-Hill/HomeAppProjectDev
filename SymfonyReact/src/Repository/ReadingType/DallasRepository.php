<?php


namespace App\Repository\ReadingType;


use App\Entity\Devices\Devices;
use App\Entity\Sensors\ReadingTypes\Temperature;
use App\Entity\Sensors\Sensors;
use App\Entity\Sensors\SensorTypes\Dallas;
use App\HomeAppSensorCore\Interfaces\SensorInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\Expr\Join;

class DallasRepository extends EntityRepository
{
    /**
     * @param string $sensorName
     * @param Devices $devices
     * @return SensorInterface|null
     * @throws NonUniqueResultException
     */
    public function findSensorBySensorName(string $sensorName, Devices $devices): ?SensorInterface
    {
        $qb = $this->createQueryBuilder('dallas');
        $expr = $qb->expr();

        $qb->select()
            ->innerJoin(
                Sensors::class,
                'sensor',
                Join::WITH,
                'sensor.sensorNameID = dallas.sensorNameID'#
        )
            ->innerJoin(
                Devices::class,
                'device',
                Join::WITH,
                'sensor.deviceNameID = device.deviceNameID'
            )
            ->where(
                $expr->eq('sensorName', ':sensorName'),
                $expr->eq('sensors.deviceNameID', ':deviceID')
            )
            ->setParameters(
                [
                    'sensorName' => $sensorName,
                    'deviceID' => $devices
                ]
            );

        return $qb->getQuery()->getOneOrNullResult();
    }
    /**
     * @param Sensors $sensor
     * @return Dallas|null
     * @throws NonUniqueResultException
     */
    public function findDallasSensorBySensor(Sensors $sensor): ?Temperature
    {
        $qb = $this->createQueryBuilder('d');
        $expr = $qb->expr();

        $qb->select('t')
            ->innerJoin(Temperature::class, 't', Join::WITH, 't.tempID = d.tempID')
            ->innerJoin(Sensors::class, 's', Join::WITH, 's.sensorNameID = t.sensorNameID')
            ->where(
                $expr->eq('d.sensor', ':sensorObject')
            )
            ->setParameters(
                [
                    'sensorObject' => $sensor
                ]
            );

        return $qb->getQuery()->getOneOrNullResult();
    }
}
