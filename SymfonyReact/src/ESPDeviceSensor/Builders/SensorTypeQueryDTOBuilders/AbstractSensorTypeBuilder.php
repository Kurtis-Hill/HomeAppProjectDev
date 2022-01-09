<?php

namespace App\ESPDeviceSensor\Builders\SensorTypeQueryDTOBuilders;

use App\ESPDeviceSensor\Factories\ReadingTypeQueryBuilderFactory;
use App\ESPDeviceSensor\Factories\ReadingTypeQueryBuilderFactory\ReadingTypeQueryFactory;

abstract class AbstractSensorTypeBuilder
{
    protected ReadingTypeQueryFactory $readingTypeQueryFactory;

    public function __construct(ReadingTypeQueryFactory $readingTypeQueryFactory)
    {
        $this->readingTypeQueryFactory = $readingTypeQueryFactory;
    }
}
