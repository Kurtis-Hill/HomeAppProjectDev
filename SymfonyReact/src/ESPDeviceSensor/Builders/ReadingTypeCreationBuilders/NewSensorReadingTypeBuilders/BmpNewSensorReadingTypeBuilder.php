<?php

namespace App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders\NewSensorReadingTypeBuilders;

use App\ESPDeviceSensor\Entity\Sensor;
use App\ESPDeviceSensor\Entity\SensorTypes\Bmp;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorTypeInterface;

class BmpNewSensorReadingTypeBuilder extends AbstractSensorReadingTypeBuilder implements NewSensorReadingTypeBuilderInterface
{
    public function buildNewReadingTypeObjects(Sensor $sensor): SensorTypeInterface
    {
        $bmp = new Bmp();
        $this->setSensorObject($bmp, $sensor);
        $this->buildTemperatureSensor($bmp);
        $this->buildHumiditySensor($bmp);
        $this->buildLatitudeSensor($bmp);

        return $bmp;
    }
}
