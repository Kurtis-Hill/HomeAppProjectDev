<?php

namespace App\Services\Device\NewDevice;

use App\DTOs\Device\Internal\NewDeviceDTO;
use App\Entity\Device\Devices;
use App\Exceptions\Device\DeviceCreationFailureException;
use Doctrine\ORM\Exception\ORMException;
use JetBrains\PhpStorm\ArrayShape;

interface NewDeviceHandlerInterface
{
    /**
     * @throws \App\Exceptions\Device\DeviceCreationFailureException
     */
    #[ArrayShape(['validationErrors'])]
    public function processNewDevice(NewDeviceDTO $newDeviceDTO): array;

    /**
     * @throws ORMException
     */
    public function saveDevice(Devices $device, bool $sendUpdateToDevice = false): bool;
}
