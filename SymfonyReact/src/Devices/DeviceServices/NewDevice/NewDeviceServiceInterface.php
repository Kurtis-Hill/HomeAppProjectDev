<?php

namespace App\Devices\DeviceServices\NewDevice;

use App\Devices\DTO\DeviceDTO;
use App\Devices\Entity\Devices;
use App\Devices\Exceptions\DuplicateDeviceException;
use Doctrine\ORM\ORMException;

interface NewDeviceServiceInterface
{
    /**
     * @throws DuplicateDeviceException
     * @throws ORMException
     */
    public function handleNewDeviceCreation(DeviceDTO $deviceData): ?Devices;
}
