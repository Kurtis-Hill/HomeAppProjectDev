<?php

namespace App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders\NewSensorReadingTypeBuilders;

use App\ESPDeviceSensor\Entity\Sensor;
use App\ESPDeviceSensor\Entity\SensorTypes\Dallas;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorTypeInterface;

class DallasNewSensorReadingTypeBuilder extends AbstractSensorReadingTypeBuilder implements NewSensorReadingTypeBuilderInterface
{
    public function buildNewReadingTypeObjects(Sensor $sensor): SensorTypeInterface
    {
        $dallas = new Dallas();
        $this->setSensorObject($dallas, $sensor);
        $this->buildTemperatureSensor($dallas);

        return $dallas;
    }
}
