<?php

namespace App\Sensors\DTO\Internal\Sensor;

use JetBrains\PhpStorm\Immutable;

//#[Immutable]
class GetSensorQueryDTO
{
    private ?int $limit;

    private ?int $offset;

    private ?int $page;

    private ?array  $deviceIDs;

    private ?array  $deviceNames;

    private ?array  $groupIDs;

    public function __construct(
        ?int $limit = null,
        ?int $offset = null,
        ?int $page = null,
        ?array $deviceIDs = null,
        ?array $deviceNames = null,
        ?array $groupIDs = null,
    ) {
        $this->limit = $limit;
        $this->offset = $offset;
        $this->page = $page;
        $this->deviceIDs = $deviceIDs;
        $this->deviceNames = $deviceNames;
        $this->groupIDs = $groupIDs;
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

    public function getDeviceIDs(): ?array
    {
        return $this->deviceIDs;
    }

    public function getDeviceNames(): ?array
    {
        return $this->deviceNames;
    }

    public function getGroupIDs(): ?array
    {
        return $this->groupIDs;
    }

    public function setDeviceIDs(array $deviceIDs): void
    {
        $this->deviceIDs = $deviceIDs;
    }

    public function setDeviceNames(array $deviceNames): void
    {
        $this->deviceNames = $deviceNames;
    }

    public function setGroupIDs(array $groupIDs): void
    {
        $this->groupIDs = $groupIDs;
    }
}