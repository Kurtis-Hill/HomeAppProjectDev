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
        $this->buildTemperatureSensor($dallas);

        return $dallas;
    }
}
