<?php

namespace App\Sensors\Builders\ReadingTypeCreationBuilders\NewSensorReadingTypeBuilders;

use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Soil;
use App\UserInterface\Exceptions\SensorTypeBuilderFailureException;
use SebastianBergmann\Comparator\Exception;

class SoilNewSensorReadingTypeBuilder extends AbstractSensorReadingTypeBuilder implements NewSensorReadingTypeBuilderInterface
{
    public function buildNewReadingTypeObjects(Sensor $sensor): SensorTypeInterface
    {
        $soil = new Soil();
        $this->setSensorObject($soil, $sensor);
        $this->buildAnalogSensor($soil);

        return $soil;
    }
}
