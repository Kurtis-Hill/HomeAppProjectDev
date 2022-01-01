<?php

namespace App\UserInterface\Builders\CardSensorTypeQueryDTOBuilder;

use App\UserInterface\Factories\CardQueryBuilderFactories\ReadingTypeQueryFactory;

abstract class AbstractSensorTypeBuilder
{
    protected ReadingTypeQueryFactory $readingTypeQueryFactory;

    public function __construct(ReadingTypeQueryFactory $readingTypeQueryFactory)
    {
        $this->readingTypeQueryFactory = $readingTypeQueryFactory;
    }
}
