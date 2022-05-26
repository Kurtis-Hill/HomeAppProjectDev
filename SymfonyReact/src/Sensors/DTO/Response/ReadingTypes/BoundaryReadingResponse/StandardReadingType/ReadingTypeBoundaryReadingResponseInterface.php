<?php

namespace App\Sensors\DTO\Response\ReadingTypes\BoundaryReadingResponse\StandardReadingType;

interface ReadingTypeBoundaryReadingResponseInterface
{
    public function getSensorReadingTypeID(): int;

    public function getReadingType(): string;
}
