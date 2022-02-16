<?php

namespace App\Devices\DeviceServices\NewDevice;

use App\Devices\DTO\NewDeviceDTO;
use App\Devices\Entity\Devices;
use App\Devices\Exceptions\DeviceCreationFailureException;

interface NewDeviceServiceInterface
{
    /**
     * @throws DeviceCreationFailureException
     */
    public function createNewDevice(NewDeviceDTO $deviceDTO): Devices;

    public function validateNewDevice(Devices $newDevice): array;

    public function encodeAndSaveNewDevice(Devices $newDevice): bool;
}
