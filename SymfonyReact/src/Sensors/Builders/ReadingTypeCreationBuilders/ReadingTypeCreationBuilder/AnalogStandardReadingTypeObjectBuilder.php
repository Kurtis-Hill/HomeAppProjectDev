<?php

namespace App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder;

use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Analog;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\AnalogReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Exceptions\SensorTypeException;

class AnalogStandardReadingTypeObjectBuilder extends AbstractStandardReadingTypeBuilder implements ReadingTypeObjectBuilderInterface
{
    public function buildReadingTypeObject(Sensor $sensor, float|int|bool $currentReading = 10): AllSensorReadingTypeInterface
    {
        $sensorType = $sensor->getSensorTypeObject();
        if (!$sensorType instanceof AnalogReadingTypeInterface) {
            throw new SensorTypeException(
                SensorTypeException::SENSOR_TYPE_NOT_RECOGNISED_NO_NAME
            );
        }
        $analogSensor = new Analog();
//        dd($sensorType);
        $analogSensor->setCurrentReading($sensorType->getMaxAnalog() / 2);
        $analogSensor->setHighReading($sensorType->getMaxAnalog());
        $analogSensor->setLowReading($sensorType->getMinAnalog());
        $analogSensor->setCreatedAt();
        $analogSensor->setUpdatedAt();
        $analogSensor->setSensor($sensor);
        $this->setBaseReadingTypeForStandardSensor($analogSensor);

        $readingTypeRepository = $this->sensorReadingTypeRepositoryFactory->getSensorReadingTypeRepository($analogSensor->getReadingType());
        $readingTypeRepository->persist($analogSensor);

        return $analogSensor;
    }
}
