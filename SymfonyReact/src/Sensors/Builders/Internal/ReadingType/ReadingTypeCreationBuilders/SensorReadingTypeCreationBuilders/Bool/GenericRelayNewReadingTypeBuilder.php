<?php

namespace App\Sensors\Builders\Internal\ReadingType\ReadingTypeCreationBuilders\SensorReadingTypeCreationBuilders\Bool;

use App\Sensors\Builders\Internal\ReadingType\ReadingTypeCreationBuilders\SensorReadingTypeCreationBuilders\AbstractNewReadingTypeBuilder;
use App\Sensors\Builders\Internal\ReadingType\ReadingTypeCreationBuilders\SensorReadingTypeCreationBuilders\NewSensorReadingTypeBuilderInterface;

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
