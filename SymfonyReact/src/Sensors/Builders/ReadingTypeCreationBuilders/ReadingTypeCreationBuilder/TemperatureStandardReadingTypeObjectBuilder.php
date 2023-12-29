<?php

namespace App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder;

use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Temperature;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\TemperatureReadingTypeInterface;
use App\Sensors\Exceptions\SensorReadingTypeRepositoryFactoryException;
use App\Sensors\Exceptions\SensorTypeException;
use Doctrine\ORM\Exception\ORMException;

class TemperatureStandardReadingTypeObjectBuilder extends AbstractStandardReadingTypeBuilder implements ReadingTypeObjectBuilderInterface
{
    /**
     * @throws SensorReadingTypeRepositoryFactoryException
     * @throws SensorTypeException
     * @throws ORMException
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
        $temperatureSensor->setCreatedAt();
        $temperatureSensor->setUpdatedAt();
        $temperatureSensor->setSensor($sensor);
//        dd($sensorType->getMaxTemperature(), $temperatureSensor, $temperatureSensor->getLowReading(), 'ehere');
        $this->setBaseReadingTypeForStandardSensor($temperatureSensor);


        $readingTypeRepository = $this->sensorReadingTypeRepositoryFactory->getSensorReadingTypeRepository($temperatureSensor->getReadingType());
        $readingTypeRepository->persist($temperatureSensor);

        return $temperatureSensor;
    }
}
