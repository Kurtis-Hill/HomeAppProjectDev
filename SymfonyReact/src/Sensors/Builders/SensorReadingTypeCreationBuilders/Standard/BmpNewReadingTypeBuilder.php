<?php

namespace App\Sensors\Builders\SensorReadingTypeCreationBuilders\Standard;

use App\Sensors\Builders\SensorReadingTypeCreationBuilders\AbstractNewReadingTypeBuilder;
use App\Sensors\Builders\SensorReadingTypeCreationBuilders\NewSensorReadingTypeBuilderInterface;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;

class BmpNewReadingTypeBuilder extends AbstractNewReadingTypeBuilder implements NewSensorReadingTypeBuilderInterface
{
    public function buildNewSensorTypeObjects(Sensor $sensor): SensorTypeInterface
    {
        $bmp = new Bmp();
        $bmp->setSensor($sensor);
        $this->buildSensorReadingTypeObjects($bmp);

        return $bmp;
    }
}
