<?php

namespace App\DTOs\Sensor\Request\CurrentReadingRequest\ReadingTypes;

use App\Entity\Sensor\SensorTypes\GenericMotion;
use App\Entity\Sensor\SensorTypes\GenericRelay;
use App\Services\CustomValidators\Sensor\SensorDataValidators\BoolConstraint;

class BoolCurrentReadingUpdateRequestDTO extends AbstractCurrentReadingUpdateRequestDTO
{
    #[BoolConstraint(groups: [GenericRelay::NAME, GenericMotion::NAME])]
    protected mixed $readingTypeCurrentReading;

    private string $readingType;

    public function __construct(mixed $readingTypeCurrentReading, mixed $readingType)
    {
        $this->readingType = $readingType;
        parent::__construct($readingTypeCurrentReading);
    }

    public function getReadingType(): mixed
    {
        return $this->readingType;
    }
}
