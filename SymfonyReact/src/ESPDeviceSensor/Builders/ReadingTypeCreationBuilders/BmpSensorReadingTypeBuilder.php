<?php

namespace App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders;

use App\ESPDeviceSensor\Entity\Sensor;
use App\ESPDeviceSensor\Entity\SensorTypes\Bmp;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\ESPDeviceSensor\Exceptions\SensorTypeBuilderFailureException;
use Exception;

class BmpSensorReadingTypeBuilder extends AbstractSensorReadingTypeBuilder implements SensorReadingTypeBuilderInterface
{
    public function buildReadingTypeObjects(Sensor $sensor): SensorTypeInterface
    {
        $bmp = new Bmp();
        $this->setSensorObject($bmp, $sensor);
        $this->buildTemperatureSensor($bmp);
        $this->buildHumiditySensor($bmp);
        $this->buildLatitudeSensor($bmp);

        return $bmp;
    }

}
