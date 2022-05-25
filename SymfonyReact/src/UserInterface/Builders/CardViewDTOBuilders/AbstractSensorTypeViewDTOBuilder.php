<?php

namespace App\UserInterface\Builders\CardViewDTOBuilders;

use App\UserInterface\Factories\SensorTypeCardDTOBuilderFactory\SensorTypeDTOBuilderFactory;

abstract class AbstractSensorTypeViewDTOBuilder
{
    protected SensorTypeDTOBuilderFactory $sensorTypeDTOBuilderFactory;

    public function __construct(SensorTypeDTOBuilderFactory $sensorTypeDTOBuilderFactory)
    {
        $this->sensorTypeDTOBuilderFactory = $sensorTypeDTOBuilderFactory;
    }
}
