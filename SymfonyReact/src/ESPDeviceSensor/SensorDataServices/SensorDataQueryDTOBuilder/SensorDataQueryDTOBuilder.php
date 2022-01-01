<?php

namespace App\ESPDeviceSensor\SensorDataServices\SensorDataQueryDTOBuilder;

use App\ESPDeviceSensor\Factories\SensorTypeObjectsBuilderFactory;

class SensorDataQueryDTOBuilder
{
    private SensorTypeObjectsBuilderFactory $sensorTypeObjectsBuilderFactory;
    public function __construct(
        SensorTypeObjectsBuilderFactory $sensorTypeObjectsBuilderFactory
    ){
        $this->sensorTypeObjectsBuilderFactory = $sensorTypeObjectsBuilderFactory;
    }
}
