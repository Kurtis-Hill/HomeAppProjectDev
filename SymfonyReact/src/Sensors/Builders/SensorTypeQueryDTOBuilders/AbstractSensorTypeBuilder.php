<?php

namespace App\Sensors\Builders\SensorTypeQueryDTOBuilders;

use App\Sensors\Factories\ReadingTypeQueryBuilderFactory;
use App\Sensors\Factories\ReadingTypeQueryBuilderFactory\ReadingTypeQueryFactory;

// Not used
abstract class AbstractSensorTypeBuilder
{
    protected ReadingTypeQueryFactory $readingTypeQueryFactory;

    public function __construct(ReadingTypeQueryFactory $readingTypeQueryFactory)
    {
        $this->readingTypeQueryFactory = $readingTypeQueryFactory;
    }
}
