<?php

namespace App\Sensors\Builders\SensorReadingTypeCreationBuilders;

use App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder\HumidityReadingTypeObjectBuilder;
use App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder\TemperatureReadingTypeObjectBuilder;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;

class DhtNewReadingTypeBuilder extends AbstractNewReadingTypeBuilder implements NewSensorReadingTypeBuilderInterface
{
    public function buildNewSensorTypeObjects(Sensor $sensor): SensorTypeInterface
    {
        $dht = new Dht();
        $dht->setSensor($sensor);
        $this->buildStandardSensorReadingTypeObjects($dht, Dht::LOW_TEMPERATURE_READING_BOUNDARY);

        return $dht;
    }
}
