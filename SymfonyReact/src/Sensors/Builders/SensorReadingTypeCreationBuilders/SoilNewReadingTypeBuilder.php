<?php

namespace App\Sensors\Builders\SensorReadingTypeCreationBuilders;

use App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder\AnalogReadingTypeObjectBuilder;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Soil;

class SoilNewReadingTypeBuilder implements NewSensorReadingTypeBuilderInterface
{
    private AnalogReadingTypeObjectBuilder $analogReadingTypeObjectBuilder;

    public function __construct(AnalogReadingTypeObjectBuilder $analogReadingTypeObjectBuilder)
    {
        $this->analogReadingTypeObjectBuilder = $analogReadingTypeObjectBuilder;
    }

    public function buildNewReadingTypeObjects(Sensor $sensor): SensorTypeInterface
    {
        $soil = new Soil();
        $soil->setSensorObject($sensor);
        $this->analogReadingTypeObjectBuilder->buildReadingTypeObject($soil);

        return $soil;
    }
}
