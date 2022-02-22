<?php

namespace App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders\NewSensorReadingTypeBuilders;

use App\ESPDeviceSensor\Entity\Sensor;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Soil;
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
