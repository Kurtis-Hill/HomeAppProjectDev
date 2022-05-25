<?php

namespace App\Devices\DeviceServices\NewDevice;

use App\Devices\DTO\Internal\NewDeviceDTO;
use App\Devices\DTO\Request\DeviceRequestDTOInterface;
use App\Devices\Entity\Devices;
use App\Devices\Exceptions\DeviceCreationFailureException;
use JetBrains\PhpStorm\ArrayShape;

interface NewDeviceServiceInterface
{
    /**
     * @throws DeviceCreationFailureException
     */
    #[ArrayShape(['validationErrors'])]
    public function processNewDevice(NewDeviceDTO $deviceDTO): array;

    public function saveNewDevice(Devices $device): bool;
}
