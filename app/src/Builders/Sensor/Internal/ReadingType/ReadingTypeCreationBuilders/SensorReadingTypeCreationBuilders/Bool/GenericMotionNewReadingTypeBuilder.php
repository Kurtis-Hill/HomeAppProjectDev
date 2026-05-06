<?php

namespace App\Builders\Sensor\Internal\ReadingType\ReadingTypeCreationBuilders\SensorReadingTypeCreationBuilders\Bool;

use App\Builders\Sensor\Internal\ReadingType\ReadingTypeCreationBuilders\SensorReadingTypeCreationBuilders\AbstractNewReadingTypeBuilder;
use App\Builders\Sensor\Internal\ReadingType\ReadingTypeCreationBuilders\SensorReadingTypeCreationBuilders\NewSensorReadingTypeBuilderInterface;

readonly class GenericMotionNewReadingTypeBuilder extends AbstractNewReadingTypeBuilder implements NewSensorReadingTypeBuilderInterface
{
//    private MotionReadingTypeReadingTypeBuilder $motionReadingTypeObjectBuilder;
//
//    public function __construct(MotionReadingTypeReadingTypeBuilder $motionReadingTypeObjectBuilder)
//    {
//        $this->motionReadingTypeObjectBuilder = $motionReadingTypeObjectBuilder;
//    }
//
//    public function buildNewSensorTypeObjects(Sensor $sensor): SensorTypeInterface
//    {
//        $genericMotion = new GenericMotion();
//        $genericMotion->setSensor($sensor);
//
//        $this->motionReadingTypeObjectBuilder->buildReadingTypeObject($genericMotion);
//
//        return $genericMotion;
//    }
}
