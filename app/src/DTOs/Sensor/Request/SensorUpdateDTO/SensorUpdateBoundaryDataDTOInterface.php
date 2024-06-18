<?php

namespace App\DTOs\Sensor\Request\SensorUpdateDTO;

interface SensorUpdateBoundaryDataDTOInterface
{
    public function getReadingType(): mixed;

    public function getConstRecord(): mixed;
}
