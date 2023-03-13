<?php

namespace App\Sensors\DTO\Request\GetSensorRequestDTO;

use App\Devices\DeviceServices\GetDevices\GetDevicesForUserInterface;
use Symfony\Component\Validator\Constraints as Assert;

class GetSensorRequestDTO
{
    #[
        Assert\Range(
            notInRangeMessage: 'limit must be greater than {{ min }} but less than {{ max }}',
            invalidMessage: 'limit must be an int|null you have provided {{ value }}',
            min: 1,
            max: GetDevicesForUserInterface::MAX_DEVICE_RETURN_SIZE
        ),
    ]
    private mixed $limit;

    #[
        Assert\Range(
            minMessage: 'offset must be greater than {{ min }}',
            invalidMessage: 'offset must be an int|null you have provided {{ value }}',
            min: 0,
        ),
    ]
    private mixed $offset;

    #[
        Assert\Type(type: ['array', "null"], message: 'deviceIDs must be a {{ type }} you have provided {{ value }}'),
    ]
    private mixed $deviceIDs;

    #[
        Assert\Type(type: ['array', "null"], message: 'deviceNames must be a {{ type }} you have provided {{ value }}'),
    ]
    private mixed $deviceNames;

    #[
        Assert\Type(type: ['array', "null"], message: 'groupIDs must be a {{ type }} you have provided {{ value }}'),
    ]
    private mixed $groupIDs;

    public function getLimit(): mixed
    {
        return $this->limit;
    }

    public function setLimit(mixed $limit): void
    {
        $this->limit = $limit;
    }

    public function getOffset(): mixed
    {
        return $this->offset;
    }

    public function setOffset(mixed $offset): void
    {
        $this->offset = $offset;
    }

    public function getDeviceIDs(): mixed
    {
        return $this->deviceIDs;
    }

    public function setDeviceIDs(mixed $deviceIDs): void
    {
        $this->deviceIDs = $deviceIDs;
    }

    public function getDeviceNames(): mixed
    {
        return $this->deviceNames;
    }

    public function setDeviceNames(mixed $deviceNames): void
    {
        $this->deviceNames = $deviceNames;
    }

    public function getGroupIDs(): mixed
    {
        return $this->groupIDs;
    }

    public function setGroupIDs(mixed $groupIDs): void
    {
        $this->groupIDs = $groupIDs;
    }
}
