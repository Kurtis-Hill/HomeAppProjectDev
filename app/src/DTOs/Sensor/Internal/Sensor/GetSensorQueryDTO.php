<?php

namespace App\DTOs\Sensor\Internal\Sensor;

use App\Entity\Sensor\Sensor;
use JetBrains\PhpStorm\ArrayShape;

class GetSensorQueryDTO
{
    private ?int $limit;

    private ?int $offset;

    private ?int $page;

    private ?array  $deviceIDs;

    private ?array  $deviceNames;

    private ?array  $groupIDs;

    private ?array  $cardViewIDs;

    #[ArrayShape([Sensor::class])]
    private array $sensorResult = [];

    public function __construct(
        ?int $limit = null,
        ?int $offset = null,
        ?int $page = null,
        ?array $deviceIDs = null,
        ?array $deviceNames = null,
        ?array $groupIDs = null,
        ?array $cardViewIDs = null
    ) {
        $this->limit = $limit;
        $this->offset = $offset;
        $this->page = $page;
        $this->deviceIDs = $deviceIDs;
        $this->deviceNames = $deviceNames;
        $this->groupIDs = $groupIDs;
        $this->cardViewIDs = $cardViewIDs;
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

    public function getCardViewIDs(): ?array
    {
        return $this->cardViewIDs;
    }

    public function setCardViewIDs(array $cardViewIDs): void
    {
        $this->cardViewIDs = $cardViewIDs;
    }
}
