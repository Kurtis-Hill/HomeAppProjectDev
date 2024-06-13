<?php

namespace App\Builders\Sensor\Internal\ReadingType\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder;

use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Humidity;
use App\Entity\Sensor\Sensor;
use App\Entity\Sensor\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Entity\Sensor\SensorTypes\Interfaces\HumidityReadingTypeInterface;
use App\Exceptions\Sensor\SensorReadingTypeRepositoryFactoryException;
use App\Exceptions\Sensor\SensorTypeException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

class HumidityStandardReadingTypeObjectBuilder extends AbstractStandardReadingTypeBuilder implements ReadingTypeObjectBuilderInterface
{
    /**
     * @throws OptimisticLockException
     * @throws \App\Exceptions\Sensor\SensorTypeException
     * @throws ORMException
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \App\Exceptions\Sensor\SensorReadingTypeRepositoryFactoryException
     */
    public function buildReadingTypeObject(Sensor $sensor, int|float|bool $currentReading = 10): AllSensorReadingTypeInterface
    {
        $sensorType = $sensor->getSensorTypeObject();
        if (!$sensorType instanceof HumidityReadingTypeInterface) {
            throw new SensorTypeException(
                SensorTypeException::SENSOR_TYPE_NOT_RECOGNISED_NO_NAME
            );
        }
        $humiditySensor = new Humidity();
        $humiditySensor->setCurrentReading($currentReading);
        $humiditySensor->setHighReading($sensorType->getMaxHumidity());
        $humiditySensor->setLowReading($sensorType->getMinHumidity());
//        $humiditySensor->setUpdatedAt();
//        $humiditySensor->setCreatedAt();
//        $humiditySensor->setSensor($sensor);
        $this->setBaseReadingTypeForStandardSensor($humiditySensor, $sensor);

        $readingTypeRepository = $this->sensorReadingTypeRepositoryFactory->getSensorReadingTypeRepository($humiditySensor->getReadingType());
        $readingTypeRepository->persist($humiditySensor);

        return $humiditySensor;
    }
}
