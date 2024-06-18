<?php

namespace App\Builders\Sensor\Internal\SensorTypeQueryDTOBuilders;

use App\Factories\Sensor\ReadingTypeQueryBuilderFactory\ReadingTypeQueryFactory;

// Not used
abstract class AbstractSensorTypeBuilder
{
    protected ReadingTypeQueryFactory $readingTypeQueryFactory;

    public function __construct(ReadingTypeQueryFactory $readingTypeQueryFactory)
    {
        $this->readingTypeQueryFactory = $readingTypeQueryFactory;
    }
}
