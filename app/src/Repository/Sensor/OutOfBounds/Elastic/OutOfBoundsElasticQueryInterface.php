<?php

declare(strict_types=1);

namespace App\Repository\Sensor\OutOfBounds\Elastic;

use DateTimeInterface;
use Elastica\Index;
use Elastica\Query;

interface OutOfBoundsElasticQueryInterface
{
    public function findByReadingsBeyondThreshold(float $threshold, string $direction, int $limit, int $offset): array;

    public function findByDateRange(DateTimeInterface $from, DateTimeInterface $to, int $limit, int $offset): array;

    public function findBySensorReadingId(int $sensorReadingId): array;

    public function findAllPaginated(int $limit, int $offset): array;

    public function search(Query $query): array;

    public function getIndex(): Index;
}
