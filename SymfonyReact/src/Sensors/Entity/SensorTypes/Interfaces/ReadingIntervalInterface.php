<?php

namespace App\Sensors\Entity\SensorTypes\Interfaces;

interface ReadingIntervalInterface
{
    public const DEFAULT_READING_INTERVAL = 6000;

    public function getReadingInterval(): int;

    public function setReadingInterval(int $readingInterval): void;
}
