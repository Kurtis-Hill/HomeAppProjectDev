<?php

namespace App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes;

use App\Sensors\Entity\SensorTypes\GenericMotion;
use App\Sensors\Entity\SensorTypes\GenericRelay;
use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\BoolConstraint;

class BoolCurrentReadingUpdateRequestDTO extends AbstractCurrentReadingUpdateRequestDTO
{
    #[BoolConstraint(groups: [GenericRelay::NAME, GenericMotion::NAME])]
    protected mixed $readingTypeCurrentReading;

    private string $readingType;

    public function __construct(mixed $readingTypeCurrentReading, string $readingType)
    {
        $this->readingType = $readingType;
        parent::__construct($readingTypeCurrentReading);
    }
    public function getReadingType(): string
    {
        return $this->readingType;
    }
}
