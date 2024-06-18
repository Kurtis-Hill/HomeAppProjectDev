<?php

namespace App\Builders\Sensor\Internal\ReadingType\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder;

use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Temperature;
use App\Entity\Sensor\Sensor;
use App\Entity\Sensor\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Entity\Sensor\SensorTypes\Interfaces\TemperatureReadingTypeInterface;
use App\Exceptions\Sensor\SensorReadingTypeRepositoryFactoryException;
use App\Exceptions\Sensor\SensorTypeException;
use Doctrine\ORM\OptimisticLockException;

class TemperatureStandardReadingTypeObjectBuilder extends AbstractStandardReadingTypeBuilder implements ReadingTypeObjectBuilderInterface
{
    /**
     * @throws OptimisticLockException
     * @throws SensorTypeException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws \App\Exceptions\Sensor\SensorReadingTypeRepositoryFactoryException
     */
    public function buildReadingTypeObject(Sensor $sensor): AllSensorReadingTypeInterface
    {
        $sensorType = $sensor->getSensorTypeObject();
        if (!$sensorType instanceof TemperatureReadingTypeInterface) {
            throw new SensorTypeException(
                SensorTypeException::SENSOR_TYPE_NOT_RECOGNISED_NO_NAME
            );
        }
        $temperatureSensor = new Temperature();
        $temperatureSensor->setCurrentReading($sensorType->getMaxTemperature() / 2);
        $temperatureSensor->setHighReading($sensorType->getMaxTemperature());
        $temperatureSensor->setLowReading($sensorType->getMinTemperature());
//        $temperatureSensor->setCreatedAt();
//        $temperatureSensor->setUpdatedAt();
//        $temperatureSensor->setSensor($sensor);
        $this->setBaseReadingTypeForStandardSensor($temperatureSensor, $sensor);


        $readingTypeRepository = $this->sensorReadingTypeRepositoryFactory->getSensorReadingTypeRepository($temperatureSensor->getReadingType());
        $readingTypeRepository->persist($temperatureSensor);

        return $temperatureSensor;
    }
}
