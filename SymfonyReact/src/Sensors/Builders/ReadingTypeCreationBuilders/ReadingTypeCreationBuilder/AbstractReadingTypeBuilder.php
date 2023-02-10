<?php

namespace App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder;

use App\Sensors\Entity\ReadingTypes\Analog;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Interfaces\AnalogSensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Exceptions\SensorTypeException;
use App\Sensors\Factories\SensorReadingType\SensorReadingTypeRepositoryFactory;

class AbstractReadingTypeBuilder
{
    protected SensorReadingTypeRepositoryFactory $sensorReadingTypeRepositoryFactory;

    public function __construct(SensorReadingTypeRepositoryFactory $readingTypeFactory)
    {
        $this->sensorReadingTypeRepositoryFactory = $readingTypeFactory;
    }

    /**
     * @throws SensorTypeException
     */
    public function buildAnalogSensor(AnalogSensorTypeInterface $analogSensorType, int|float $currentReading = 1000): void
    {
        if (!$analogSensorType instanceof SensorTypeInterface) {
            throw new SensorTypeException(
                SensorTypeException::SENSOR_TYPE_NOT_RECOGNISED_NO_NAME
            );
        }
        $analogSensor = new Analog();
        $analogSensor->setCurrentReading($currentReading);
        $analogSensor->setHighReading($analogSensorType->getMaxAnalog());
        $analogSensor->setLowReading($analogSensorType->getMinAnalog());
        $analogSensor->setUpdatedAt();
        $analogSensor->setSensor($analogSensorType->getSensor());

        $analogSensorType->setAnalogObject($analogSensor);
    }

    protected function setSensorObject(SensorTypeInterface $sensorType, Sensor $sensor): void
    {
        $sensorType->setSensor($sensor);
    }
}
