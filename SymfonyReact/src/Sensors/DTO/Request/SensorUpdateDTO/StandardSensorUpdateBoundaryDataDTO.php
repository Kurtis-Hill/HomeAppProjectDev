<?php

namespace App\Sensors\DTO\Request\SensorUpdateDTO;

use Symfony\Component\Validator\Constraints as Assert;

class StandardSensorUpdateBoundaryDataDTO implements SensorUpdateBoundaryDataDTOInterface
{
    #[
        Assert\Type(type: 'string', message: 'readingType must be a {{ type }} you have provided {{ value }}'),
        Assert\NotNull(
            message: "readingType cannot be null"
        ),
    ]
    private mixed $readingType;

    #[
        Assert\Type(type: ['integer', 'null'], message: 'highReading must be a {{ type }} you have provided {{ value }}'),
    ]
    private mixed $highReading;

    #[
        Assert\Type(type: ['integer', 'null'], message: 'lowReading must be a {{ type }} you have provided {{ value }}'),
    ]
    private mixed $lowReading;

    #[
        Assert\Type(type: ['bool', 'null'], message: 'constRecord must be a {{ type }} you have provided {{ value }}'),
    ]
    private mixed $constRecord;

    public function __construct(
        mixed $readingType,
        mixed $highReading,
        mixed $lowReading,
        mixed $constRecord
    ) {
        $this->readingType = $readingType;
        $this->highReading = $highReading;
        $this->lowReading = $lowReading;
        $this->constRecord = $constRecord;
    }

    public function getReadingType(): mixed
    {
        return $this->readingType;
    }


    public function getHighReading(): mixed
    {
        return $this->highReading;
    }

    public function getLowReading(): mixed
    {
        return $this->lowReading;
    }

    public function getConstRecord(): mixed
    {
        return $this->constRecord;
    }
}
