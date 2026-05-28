<?php

declare(strict_types=1);

namespace App\DTOs\Sensor\Response\OutOfBounds;

class OutOfBoundsReadingResponseDTO
{
    public function __construct(
        public readonly int $sensorReadingID,
        public readonly float $sensorReading,
        public readonly string $createdAt,
        public readonly string $readingType,
    ) {
    }

    public static function fromElasticHit(array $hit, string $readingType): self
    {
        return new self(
            sensorReadingID: (int) $hit['sensorReadingID'],
            sensorReading: (float) $hit['sensorReading'],
            createdAt: $hit['createdAt'],
            readingType: $readingType,
        );
    }
}
