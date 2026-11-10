<?php

namespace App\DTOs\Sensor\Request\SensorUpdateDTO;

use App\Entity\Sensor\ReadingTypes\ReadingTypes;
use JetBrains\PhpStorm\Immutable;
use Symfony\Component\Validator\Constraints as Assert;

#[Immutable]
class StandardSensorUpdateBoundaryDataDTO implements SensorUpdateBoundaryDataDTOInterface
{
    public function __construct(
        #[
            Assert\Type(type: 'string', message: 'readingType must be a {{ type }} you have provided {{ value }}'),
            Assert\NotNull(
                message: "readingType cannot be null"
            ),
            Assert\Choice(choices: ReadingTypes::ALL_READING_TYPES, message: 'Choose a valid reading type.'),
        ]
        private mixed $readingType,
        #[
            Assert\Type(type: ['integer', 'null'], message: 'highReading must be a {{ type }} you have provided {{ value }}'),
        ]
        private mixed $highReading,
        #[
            Assert\Type(type: ['integer', 'null'], message: 'lowReading must be a {{ type }} you have provided {{ value }}'),
        ]
        private mixed $lowReading,
        #[
            Assert\Type(type: ['bool', 'null'], message: 'constRecord must be a {{ type }} you have provided {{ value }}'),
        ]
        private mixed $constRecord,
        #[
            Assert\Type(type: ['integer', 'null'], message: 'outOfBoundsAlertTimer must be a {{ type }} you have provided {{ value }}'),
        ]
        private mixed $outOfBoundsAlertTimer,
    ) {
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

    public function getOutOfBoundsAlertTimer(): mixed
    {
        return $this->outOfBoundsAlertTimer;
    }
}
