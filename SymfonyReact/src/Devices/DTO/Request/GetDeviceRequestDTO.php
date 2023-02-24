<?php

namespace App\Devices\DTO\Request;

use App\Devices\DeviceServices\GetDevices\GetDevicesForUserHandler;
use App\Devices\DeviceServices\GetDevices\GetDevicesForUserInterface;
use Symfony\Component\Validator\Constraints as Assert;

class GetDeviceRequestDTO
{
    #[
//        Assert\Type(
//            type: ['int', "null"],
//            message: 'limit must be an {{ type }} you have provided {{ value }}'),
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

    public function __construct(
        mixed $limit,
        mixed $offset
    ) {
        $this->limit = $limit;
        $this->offset = $offset;
    }

    public function getLimit(): mixed
    {
        return $this->limit;
    }

    public function getOffset(): mixed
    {
        return $this->offset;
    }

    public function setLimit(mixed $limit): void
    {
        $this->limit = $limit;
    }

    public function setOffset(mixed $offset): void
    {
        $this->offset = $offset;
    }
}
