<?php

namespace App\Sensors\Builders\ReadingTypeCreationBuilders\NewSensorReadingTypeBuilders;

use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\UserInterface\Exceptions\SensorTypeBuilderFailureException;
use Exception;

class DhtNewSensorReadingTypeBuilder extends AbstractSensorReadingTypeBuilder implements NewSensorReadingTypeBuilderInterface
{
    public function buildNewReadingTypeObjects(Sensor $sensor): SensorTypeInterface
    {
        $dht = new Dht();
        $this->setSensorObject($dht, $sensor);
        $this->buildTemperatureSensor($dht);
        $this->buildHumiditySensor($dht);

        return $dht;
    }
}
