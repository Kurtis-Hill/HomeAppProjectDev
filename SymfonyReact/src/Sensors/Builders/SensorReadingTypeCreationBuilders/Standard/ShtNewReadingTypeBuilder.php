<?php

namespace App\Sensors\Builders\SensorReadingTypeCreationBuilders\Standard;

use App\Sensors\Builders\SensorReadingTypeCreationBuilders\AbstractNewReadingTypeBuilder;
use App\Sensors\Builders\SensorReadingTypeCreationBuilders\NewSensorReadingTypeBuilderInterface;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Sht;

class ShtNewReadingTypeBuilder extends AbstractNewReadingTypeBuilder implements NewSensorReadingTypeBuilderInterface
{
    public function buildNewSensorTypeObjects(Sensor $sensor): SensorTypeInterface
    {
        $sht = new Sht();
        $sht->setSensor($sensor);
        $this->buildSensorReadingTypeObjects($sht);

        return $sht;
    }
}
