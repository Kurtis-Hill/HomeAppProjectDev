<?php

namespace App\Sensors\DTO\Request\SensorUpdateDTO;
use JetBrains\PhpStorm\Immutable;
use Symfony\Component\Validator\Constraints as Assert;

#[Immutable]
class BoolSensorUpdateBoundaryDataDTO implements SensorUpdateBoundaryDataDTOInterface
{
    #[
        Assert\Type(type: 'string', message: 'readingType must be a {{ type }} you have provided {{ value }}'),
        Assert\NotNull(
            message: "readingType cannot be null"
        ),
    ]
    private mixed $readingType;

    #[
        Assert\Type(type: ['bool', 'null'], message: 'expectedReading must be a {{ type }} you have provided {{ value }}'),
    ]
    private mixed $expectedReading;

    #[
        Assert\Type(type: ['bool', 'null'], message: 'constRecord must be a {{ type }} you have provided {{ value }}'),
    ]
    private mixed $constRecord;

    public function __construct(
        mixed $readingType,
        mixed $expectedReading,
        mixed $constRecord
    ) {
        $this->readingType = $readingType;
        $this->expectedReading = $expectedReading;
        $this->constRecord = $constRecord;
    }

    public function getReadingType(): mixed
    {
        return $this->readingType;
    }

    public function getExpectedReading(): mixed
    {
        return $this->expectedReading;
    }

    public function getConstRecord(): mixed
    {
        return $this->constRecord;
    }
}
