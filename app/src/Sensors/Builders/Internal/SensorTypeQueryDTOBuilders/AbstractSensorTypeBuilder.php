<?php

namespace App\Sensors\Builders\Internal\SensorTypeQueryDTOBuilders;

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
