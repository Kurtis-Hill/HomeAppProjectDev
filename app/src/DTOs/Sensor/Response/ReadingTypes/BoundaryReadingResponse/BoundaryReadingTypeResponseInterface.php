<?php

namespace App\DTOs\Sensor\Response\ReadingTypes\BoundaryReadingResponse;

interface BoundaryReadingTypeResponseInterface
{
    public function getSensorReadingTypeID(): int;

    public function getReadingType(): string;
}
