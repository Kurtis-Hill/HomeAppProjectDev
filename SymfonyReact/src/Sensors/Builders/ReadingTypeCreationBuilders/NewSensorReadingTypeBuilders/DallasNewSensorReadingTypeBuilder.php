<?php

namespace App\Sensors\Builders\ReadingTypeCreationBuilders\NewSensorReadingTypeBuilders;

use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;

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
