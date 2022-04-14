<?php

namespace App\UserInterface\Builders\CardViewBuilders;

use App\UserInterface\Factories\SensorTypeCardDTOBuilderFactory\SensorTypeDTOBuilderFactory;

abstract class AbstractSensorTypeViewDTOBuilder
{
    protected SensorTypeDTOBuilderFactory $sensorTypeDTOBuilderFactory;

    public function __construct(SensorTypeDTOBuilderFactory $sensorTypeDTOBuilderFactory)
    {
        $this->sensorTypeDTOBuilderFactory = $sensorTypeDTOBuilderFactory;
    }
}
