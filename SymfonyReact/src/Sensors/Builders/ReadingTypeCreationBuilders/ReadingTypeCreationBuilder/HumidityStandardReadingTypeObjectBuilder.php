<?php

namespace App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder;

use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Humidity;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\HumidityReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Exceptions\SensorReadingTypeRepositoryFactoryException;
use App\Sensors\Exceptions\SensorTypeException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

class HumidityStandardReadingTypeObjectBuilder extends AbstractStandardReadingTypeBuilder implements ReadingTypeObjectBuilderInterface
{
    /**
     * @throws OptimisticLockException
     * @throws SensorTypeException
     * @throws ORMException
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws SensorReadingTypeRepositoryFactoryException
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
