<?php

declare(strict_types=1);

namespace App\DTOs\Sensor\Request\OutOfBounds;

use DateTimeImmutable;
use DateTimeInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class GetOutOfBoundsReadingsRequestDTO
{
    public const VALID_READING_TYPES = ['temperature', 'humidity', 'analog', 'latitude'];

    public const DIRECTION_ABOVE = 'above';

    public const DIRECTION_BELOW = 'below';

    public const VALID_DIRECTIONS = [self::DIRECTION_ABOVE, self::DIRECTION_BELOW];

    public const DEFAULT_LIMIT = 500;

    public const MAX_LIMIT = 1000;

    #[Assert\Type(type: ['array', 'null'], message: 'readingTypes must be an array or null')]
    #[Assert\All(constraints: [
        new Assert\Choice(
            choices: self::VALID_READING_TYPES,
            message: 'Reading type "{{ value }}" is not valid. Valid types are: temperature, humidity, analog, latitude',
        ),
    ])]
    private ?array $readingTypes = null;

    #[Assert\Type(type: ['float', 'integer', 'null'], message: 'threshold must be a numeric value or null')]
    private ?float $threshold = null;

    // Direction is validated in the #[Assert\Callback] below (null is allowed)
    private ?string $direction = null;

    private ?string $startDate = null;

    private ?string $endDate = null;

    #[Assert\Positive(message: 'sensorReadingID must be a positive integer')]
    private ?int $sensorReadingID = null;

    #[Assert\Range(
        notInRangeMessage: 'limit must be between {{ min }} and {{ max }}',
        min: 1,
        max: self::MAX_LIMIT,
    )]
    private int $limit = self::DEFAULT_LIMIT;

    #[Assert\PositiveOrZero(message: 'offset must be zero or a positive integer')]
    private int $offset = 0;

    public function getReadingTypes(): array
    {
        return $this->readingTypes ?? self::VALID_READING_TYPES;
    }

    public function setReadingTypes(?array $readingTypes): void
    {
        $this->readingTypes = $readingTypes;
    }

    public function getThreshold(): ?float
    {
        return $this->threshold;
    }

    public function setThreshold(?float $threshold): void
    {
        $this->threshold = $threshold;
    }

    public function getDirection(): ?string
    {
        return $this->direction;
    }

    public function setDirection(?string $direction): void
    {
        $this->direction = $direction;
    }

    public function getStartDate(): ?DateTimeInterface
    {
        if ($this->startDate === null) {
            return null;
        }

        return new DateTimeImmutable($this->startDate);
    }

    public function setStartDate(?string $startDate): void
    {
        $this->startDate = $startDate;
    }

    public function getEndDate(): ?DateTimeInterface
    {
        if ($this->endDate === null) {
            return null;
        }

        return new DateTimeImmutable($this->endDate);
    }

    public function setEndDate(?string $endDate): void
    {
        $this->endDate = $endDate;
    }

    public function getSensorReadingID(): ?int
    {
        return $this->sensorReadingID;
    }

    public function setSensorReadingID(?int $sensorReadingID): void
    {
        $this->sensorReadingID = $sensorReadingID;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function setLimit(int $limit): void
    {
        $this->limit = $limit;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function setOffset(int $offset): void
    {
        $this->offset = $offset;
    }

    #[Assert\Callback]
    public function validate(ExecutionContextInterface $context): void
    {
        if ($this->direction !== null && !in_array($this->direction, self::VALID_DIRECTIONS, true)) {
            $context
                ->buildViolation('Direction must be one of: above, below')
                ->atPath('direction')
                ->addViolation();
        }

        if ($this->threshold !== null && $this->direction === null) {
            $context
                ->buildViolation('direction is required when threshold is provided')
                ->atPath('direction')
                ->addViolation();
        }

        if ($this->startDate !== null && $this->endDate === null) {
            $context
                ->buildViolation('endDate is required when startDate is provided')
                ->atPath('endDate')
                ->addViolation();
        }

        if ($this->endDate !== null && $this->startDate === null) {
            $context
                ->buildViolation('startDate is required when endDate is provided')
                ->atPath('startDate')
                ->addViolation();
        }

        if ($this->startDate !== null) {
            try {
                new DateTimeImmutable($this->startDate);
            } catch (\Exception) {
                $context
                    ->buildViolation('startDate is not a valid date format')
                    ->atPath('startDate')
                    ->addViolation();
            }
        }

        if ($this->endDate !== null) {
            try {
                new DateTimeImmutable($this->endDate);
            } catch (\Exception) {
                $context
                    ->buildViolation('endDate is not a valid date format')
                    ->atPath('endDate')
                    ->addViolation();
            }
        }
    }
}
