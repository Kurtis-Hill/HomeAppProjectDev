<?php

namespace App\Sensors\Builders\SensorReadingTypeCreationBuilders\Bool;

use App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder\RelayReadingTypeReadingTypeBuilder;
use App\Sensors\Builders\SensorReadingTypeCreationBuilders\AbstractNewReadingTypeBuilder;
use App\Sensors\Builders\SensorReadingTypeCreationBuilders\NewSensorReadingTypeBuilderInterface;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\GenericRelay;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;

readonly class GenericRelayNewReadingTypeBuilder extends AbstractNewReadingTypeBuilder implements NewSensorReadingTypeBuilderInterface
{
//    private RelayReadingTypeReadingTypeBuilder $relayReadingTypeObjectBuilder;
//
//    public function __construct(RelayReadingTypeReadingTypeBuilder $relayReadingTypeObjectBuilder)
//    {
//        $this->relayReadingTypeObjectBuilder = $relayReadingTypeObjectBuilder;
//    }
//
//    public function buildNewSensorTypeObjects(Sensor $sensor): SensorTypeInterface
//    {
//        $genericRelay = new GenericRelay();
//        $genericRelay->setSensor($sensor);
//
//        $this->relayReadingTypeObjectBuilder->buildReadingTypeObject($genericRelay);
//
//        return $genericRelay;
//    }
}
