<?php

namespace App\DTOs\Sensor\Internal\BoundaryReadings;

interface UpdateBoundaryReadingDTOInterface
{
    public function getNewConstRecord(): ?bool;

    public function getCurrentConstRecord(): bool;
}
