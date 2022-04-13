<?php

namespace App\Sensors\SensorDataServices\SensorDataQueryDTOBuilder;

use App\Sensors\Factories\SensorTypeObjectsBuilderFactory;

class SensorDataQueryDTOBuilder
{
    private SensorTypeObjectsBuilderFactory $sensorTypeObjectsBuilderFactory;
    public function __construct(
        SensorTypeObjectsBuilderFactory $sensorTypeObjectsBuilderFactory
    ){
        $this->sensorTypeObjectsBuilderFactory = $sensorTypeObjectsBuilderFactory;
    }
}
