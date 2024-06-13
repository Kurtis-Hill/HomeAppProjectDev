<?php

namespace App\Builders\UserInterface\CardViewDTOBuilders\CurrentReadingResponse;

use App\Factories\UserInterface\SensorTypeCardDTOBuilderFactory\SensorTypeDTOBuilderFactory;

abstract class AbstractSensorTypeViewDTOBuilder
{
    protected SensorTypeDTOBuilderFactory $sensorTypeDTOBuilderFactory;

    public function __construct(SensorTypeDTOBuilderFactory $sensorTypeDTOBuilderFactory)
    {
        $this->sensorTypeDTOBuilderFactory = $sensorTypeDTOBuilderFactory;
    }
}
