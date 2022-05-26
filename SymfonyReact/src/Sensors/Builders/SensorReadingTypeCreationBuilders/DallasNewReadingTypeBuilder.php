<?php

namespace App\Sensors\Builders\SensorReadingTypeCreationBuilders;

use App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder\TemperatureReadingTypeObjectBuilder;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;

class DallasNewReadingTypeBuilder implements NewSensorReadingTypeBuilderInterface
{
    private TemperatureReadingTypeObjectBuilder $temperatureReadingTypeObjectBuilder;

    public function __construct(TemperatureReadingTypeObjectBuilder $temperatureReadingTypeObjectBuilder)
    {
        $this->temperatureReadingTypeObjectBuilder = $temperatureReadingTypeObjectBuilder;
    }

    public function buildNewReadingTypeObjects(Sensor $sensor): SensorTypeInterface
    {
        $dallas = new Dallas();
        $dallas->setSensorObject($sensor);
        $this->temperatureReadingTypeObjectBuilder->buildReadingTypeObject($dallas);

        return $dallas;
    }
}
