<?php

namespace App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders;

use App\ESPDeviceSensor\Entity\Sensor;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Soil;
use App\ESPDeviceSensor\Exceptions\SensorTypeBuilderFailureException;
use SebastianBergmann\Comparator\Exception;

class SoilSensorReadingTypeBuilder extends AbstractSensorReadingTypeBuilder implements SensorReadingTypeBuilderInterface
{
    public function buildReadingTypeObjects(Sensor $sensor): SensorTypeInterface
    {
        $soil = new Soil();
        $this->setSensorObject($soil, $sensor);

        $soil->setSensorObject($sensor);
        try {
            $this->buildAnalogSensor($soil);
        }  catch (Exception) {
            throw new SensorTypeBuilderFailureException(
                sprintf(
                    SensorTypeBuilderFailureException::SENSOR_TYPE_BUILDER_FAILURE_MESSAGE,
                    $sensor->getSensorTypeObject()->getSensorType()
                )
            );
        }

        return $soil;
    }
}
