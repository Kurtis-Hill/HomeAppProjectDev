<?php

namespace App\Builders\Sensor\Internal\ReadingType\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder;

use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Analog;
use App\Entity\Sensor\Sensor;
use App\Entity\Sensor\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Entity\Sensor\SensorTypes\Interfaces\AnalogReadingTypeInterface;
use App\Exceptions\Sensor\SensorReadingTypeRepositoryFactoryException;
use App\Exceptions\Sensor\SensorTypeException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;

class AnalogStandardReadingTypeObjectBuilder extends AbstractStandardReadingTypeBuilder implements ReadingTypeObjectBuilderInterface
{
    /**
     * @throws OptimisticLockException
     * @throws \App\Exceptions\Sensor\SensorTypeException
     * @throws ORMException
     * @throws \Doctrine\ORM\Exception\ORMException
     * @throws SensorReadingTypeRepositoryFactoryException
     */
    public function buildReadingTypeObject(Sensor $sensor, float|int|bool $currentReading = 10): AllSensorReadingTypeInterface
    {
        $sensorType = $sensor->getSensorTypeObject();
        if (!$sensorType instanceof AnalogReadingTypeInterface) {
            throw new SensorTypeException(
                SensorTypeException::SENSOR_TYPE_NOT_RECOGNISED_NO_NAME
            );
        }
        $analogSensor = new Analog();
        $analogSensor->setCurrentReading($sensorType->getMaxAnalog() / 2);
        $analogSensor->setHighReading($sensorType->getMaxAnalog());
        $analogSensor->setLowReading($sensorType->getMinAnalog());
//        $analogSensor->setCreatedAt();
//        $analogSensor->setUpdatedAt();
//        $analogSensor->setSensor($sensor);
        $this->setBaseReadingTypeForStandardSensor($analogSensor, $sensor);

        $readingTypeRepository = $this->sensorReadingTypeRepositoryFactory->getSensorReadingTypeRepository($analogSensor->getReadingType());
        $readingTypeRepository->persist($analogSensor);

        return $analogSensor;
    }
}
