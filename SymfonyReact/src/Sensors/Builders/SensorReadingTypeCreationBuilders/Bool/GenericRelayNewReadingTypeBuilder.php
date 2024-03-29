<?php

namespace App\Sensors\Builders\SensorReadingTypeCreationBuilders\Bool;

use App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder\RelayReadingTypeObjectBuilder;
use App\Sensors\Builders\SensorReadingTypeCreationBuilders\NewSensorReadingTypeBuilderInterface;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\GenericRelay;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;

class GenericRelayNewReadingTypeBuilder implements NewSensorReadingTypeBuilderInterface
{
    private RelayReadingTypeObjectBuilder $relayReadingTypeObjectBuilder;

    public function __construct(RelayReadingTypeObjectBuilder $relayReadingTypeObjectBuilder)
    {
        $this->relayReadingTypeObjectBuilder = $relayReadingTypeObjectBuilder;
    }

    public function buildNewSensorTypeObjects(Sensor $sensor): SensorTypeInterface
    {
        $genericRelay = new GenericRelay();
        $genericRelay->setSensor($sensor);

        $this->relayReadingTypeObjectBuilder->buildReadingTypeObject($genericRelay);

        return $genericRelay;
    }
}
