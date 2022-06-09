<?php

namespace App\Devices\DeviceServices\NewDevice;

use App\Devices\DTO\Internal\NewDeviceDTO;
use App\Devices\DTO\Request\DeviceRequestDTOInterface;
use App\Devices\Entity\Devices;
use App\Devices\Exceptions\DeviceCreationFailureException;
use JetBrains\PhpStorm\ArrayShape;

interface NewDeviceHandlerInterface
{
    /**
     * @throws DeviceCreationFailureException
     */
    #[ArrayShape(['validationErrors'])]
    public function processNewDevice(NewDeviceDTO $deviceDTO): array;

    public function saveDevice(Devices $device): bool;
}
