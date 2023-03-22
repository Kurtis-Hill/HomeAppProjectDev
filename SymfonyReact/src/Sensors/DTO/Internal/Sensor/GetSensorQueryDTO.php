<?php

namespace App\Sensors\DTO\Internal\Sensor;

use JetBrains\PhpStorm\Immutable;

#[Immutable]
readonly class GetSensorQueryDTO
{
    public function __construct(
        private ?int $limit = null,
        private ?int $offset = null,
        private ?int $page = null,
        private array $deviceIDs = [],
        private array $deviceNames = [],
        private array $groupIDs = []
    ) {
    }

    public function getLimit(): ?int
    {
        return $this->limit;
    }

    public function getOffset(): ?int
    {
        return $this->offset;
    }

    public function getPage(): ?int
    {
        return $this->page;
    }

    public function getDeviceIDs(): array
    {
        return $this->deviceIDs;
    }

    public function getDeviceNames(): array
    {
        return $this->deviceNames;
    }

    public function getGroupIDs(): array
    {
        return $this->groupIDs;
    }
}
