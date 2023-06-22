<?php

namespace App\Sensors\Builders\SensorReadingTypeCreationBuilders;

use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;

class BmpNewReadingTypeBuilder extends AbstractNewReadingTypeBuilder implements NewSensorReadingTypeBuilderInterface
{
    public function buildNewSensorTypeObjects(Sensor $sensor): SensorTypeInterface
    {
        $bmp = new Bmp();
        $bmp->setSensor($sensor);
        $this->buildStandardSensorReadingTypeObjects($bmp);

        return $bmp;
    }
}
