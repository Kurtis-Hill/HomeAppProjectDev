<?php

namespace App\Sensors\Builders\SensorReadingTypeCreationBuilders\Bool;

use App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder\MotionReadingTypeObjectBuilder;
use App\Sensors\Builders\SensorReadingTypeCreationBuilders\NewSensorReadingTypeBuilderInterface;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\GenericMotion;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;

class GenericMotionNewReadingTypeBuilder implements NewSensorReadingTypeBuilderInterface
{
    private MotionReadingTypeObjectBuilder $motionReadingTypeObjectBuilder;

    public function __construct(MotionReadingTypeObjectBuilder $motionReadingTypeObjectBuilder)
    {
        $this->motionReadingTypeObjectBuilder = $motionReadingTypeObjectBuilder;
    }

    public function buildNewSensorTypeObjects(Sensor $sensor): SensorTypeInterface
    {
        $genericMotion = new GenericMotion();
        $genericMotion->setSensor($sensor);

        $this->motionReadingTypeObjectBuilder->buildReadingTypeObject($genericMotion);

        return $genericMotion;
    }
}
