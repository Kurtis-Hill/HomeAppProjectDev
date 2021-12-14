<?php

namespace App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders;

use App\ESPDeviceSensor\Entity\Sensor;
use App\ESPDeviceSensor\Entity\SensorTypes\Bmp;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\ESPDeviceSensor\Exceptions\SensorTypeBuilderFailureException;
use SebastianBergmann\Comparator\Exception;

class BmpSensorReadingTypeBuilder extends AbstractSensorReadingTypeBuilder implements SensorReadingTypeBuilderInterface
{
    public function buildReadingTypeObjects(Sensor $sensor): SensorTypeInterface
    {
        $bmp = new Bmp();

        try {
            $this->buildTemperatureSensor($bmp);
            $this->buildHumiditySensor($bmp);
            $this->buildLatitudeSensor($bmp);
        } catch (Exception) {
            throw new SensorTypeBuilderFailureException(
                sprintf(
                    SensorTypeBuilderFailureException::SENSOR_TYPE_BUILDER_FAILURE_MESSAGE,
                    $sensor->getSensorTypeObject()->getSensorType()
                )
            );
        }
    }
}
