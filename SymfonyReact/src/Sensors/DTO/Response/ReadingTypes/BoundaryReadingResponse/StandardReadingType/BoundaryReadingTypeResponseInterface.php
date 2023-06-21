<?php

namespace App\Sensors\DTO\Response\ReadingTypes\BoundaryReadingResponse\StandardReadingType;

interface BoundaryReadingTypeResponseInterface
{
    public function getSensorReadingTypeID(): int;

    public function getReadingType(): string;
}
