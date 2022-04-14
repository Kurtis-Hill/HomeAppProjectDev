<?php

namespace App\Devices\DeviceServices\NewDevice;

use App\Devices\DTO\Internal\NewDeviceDTO;
use App\Devices\DTO\Request\DeviceRequestDTOInterface;
use App\Devices\Entity\Devices;
use App\Devices\Exceptions\DeviceCreationFailureException;

interface NewDeviceBuilderInterface
{
    public function validateDeviceRequestObject(DeviceRequestDTOInterface $deviceRequestDTO): array;

    /**
     * @throws DeviceCreationFailureException
     */
    public function createNewDevice(NewDeviceDTO $deviceDTO): Devices;

    public function validateNewDevice(Devices $newDevice): array;

    public function saveNewDevice(Devices $device): bool;
}
