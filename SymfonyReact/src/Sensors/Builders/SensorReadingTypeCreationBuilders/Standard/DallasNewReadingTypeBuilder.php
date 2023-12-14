<?php

namespace App\Sensors\Builders\SensorReadingTypeCreationBuilders\Standard;

use App\Sensors\Builders\SensorReadingTypeCreationBuilders\AbstractNewReadingTypeBuilder;
use App\Sensors\Builders\SensorReadingTypeCreationBuilders\NewSensorReadingTypeBuilderInterface;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;

class DallasNewReadingTypeBuilder extends AbstractNewReadingTypeBuilder implements NewSensorReadingTypeBuilderInterface
{
    public function buildNewSensorTypeObjects(Sensor $sensor): SensorTypeInterface
    {
        $this->buildSensorReadingTypeObjects($dallas);
    }
}
