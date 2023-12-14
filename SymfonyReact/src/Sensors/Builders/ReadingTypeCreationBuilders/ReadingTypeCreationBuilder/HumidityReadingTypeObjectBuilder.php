<?php

namespace App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder;

use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Humidity;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Interfaces\HumidityReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Exceptions\SensorTypeException;

class HumidityReadingTypeObjectBuilder extends AbstractReadingTypeBuilder implements ReadingTypeObjectBuilderInterface
{
    public function buildReadingTypeObject(Sensor $sensor, int|float|bool $currentReading = 10): void
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
        $humiditySensor->setUpdatedAt();
        $humiditySensor->setSensor($sensor);


        $readingTypeRepository = $this->sensorReadingTypeRepositoryFactory->getSensorReadingTypeRepository($humiditySensor->getReadingType());
        $readingTypeRepository->persist($humiditySensor);
    }
}
