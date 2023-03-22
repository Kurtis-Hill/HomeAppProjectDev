<?php

namespace App\Sensors\DTO\Request\GetSensorRequestDTO;

use App\Common\Builders\Request\RequestDTOBuilder;
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
        Assert\Type(type: ['int', "null"], message: 'page must be a {{ type }} you have provided {{ value }}'),
    ]
    private mixed $page;

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

    #[Assert\Choice(choices: [RequestDTOBuilder::REQUEST_TYPE_ONLY, RequestDTOBuilder::REQUEST_TYPE_FULL], message: 'responseType must be one of {{ choices }} you have provided {{ value }}')]
    private mixed $responseType;

    public function getLimit(): mixed
    {
        return $this->limit;
    }

    public function setLimit(mixed $limit): void
    {
        $this->limit = $limit;
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

    public function getPage(): mixed
    {
        return $this->page;
    }

    public function setPage(mixed $page): void
    {
        $this->page = $page;
    }

    public function getResponseType(): mixed
    {
        return $this->responseType ?? RequestDTOBuilder::REQUEST_TYPE_ONLY;
    }

    public function setResponseType(mixed $responseType): void
    {
        $this->responseType = $responseType;
    }
}
