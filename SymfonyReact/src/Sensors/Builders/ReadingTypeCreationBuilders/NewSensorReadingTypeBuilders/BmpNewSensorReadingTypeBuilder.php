<?php

namespace App\Sensors\Builders\ReadingTypeCreationBuilders\NewSensorReadingTypeBuilders;

use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;

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
