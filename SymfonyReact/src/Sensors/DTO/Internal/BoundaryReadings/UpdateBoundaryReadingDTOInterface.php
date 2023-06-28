<?php

namespace App\Sensors\DTO\Internal\BoundaryReadings;

interface UpdateBoundaryReadingDTOInterface
{
    public function getNewConstRecord(): ?bool;

    public function getCurrentConstRecord(): bool;
}
