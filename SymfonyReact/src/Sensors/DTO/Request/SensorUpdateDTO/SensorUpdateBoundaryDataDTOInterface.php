<?php

namespace App\Sensors\DTO\Request\SensorUpdateDTO;

interface SensorUpdateBoundaryDataDTOInterface
{
    public function getReadingType(): mixed;

    public function getConstRecord(): mixed;
}
