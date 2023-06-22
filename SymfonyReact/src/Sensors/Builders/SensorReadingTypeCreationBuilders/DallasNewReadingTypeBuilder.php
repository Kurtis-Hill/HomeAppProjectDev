<?php

namespace App\Sensors\Builders\SensorReadingTypeCreationBuilders;

use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;

class DallasNewReadingTypeBuilder extends AbstractNewReadingTypeBuilder implements NewSensorReadingTypeBuilderInterface
{
    public function buildNewSensorTypeObjects(Sensor $sensor): SensorTypeInterface
    {
        $dallas = new Dallas();
        $dallas->setSensor($sensor);
        $this->buildStandardSensorReadingTypeObjects($dallas);

        return $dallas;
    }
}
