<?php

namespace App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders;

use App\ESPDeviceSensor\Entity\Sensor;
use App\ESPDeviceSensor\Entity\SensorTypes\Dallas;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorTypeInterface;

class DallasSensorReadingTypeBuilder extends AbstractSensorReadingTypeBuilder implements SensorReadingTypeBuilderInterface
{
    public function buildReadingTypeObjects(Sensor $sensor): SensorTypeInterface
    {
        $dallas = new Dallas();
        $this->setSensorObject($dallas, $sensor);
//        try {
            $this->buildTemperatureSensor($dallas);
//        } catch (Exception) {
//            throw new SensorTypeBuilderFailureException(
//                sprintf(
//                    SensorTypeBuilderFailureException::SENSOR_TYPE_BUILDER_FAILURE_MESSAGE,
//                    $sensor->getSensorTypeObject()->getSensorType()
//                )
//            );
//        }

        return $dallas;
    }
}
