<?php

namespace App\Devices\DeviceServices\NewDevice;

use App\Devices\DTO\Internal\NewDeviceDTO;
use App\Devices\DTO\Request\DeviceRequestDTOInterface;
use App\Devices\DTO\Request\NewDeviceRequestDTO;
use App\Devices\Entity\Devices;
use App\Devices\Exceptions\DeviceCreationFailureException;
use App\User\Entity\User;
use Doctrine\ORM\Exception\ORMException;
use JetBrains\PhpStorm\ArrayShape;

interface NewDeviceHandlerInterface
{
    public function processAddDeviceObjects(NewDeviceRequestDTO $newDeviceRequestDTO, User $createdByUser): NewDeviceDTO;

    /**
     * @throws DeviceCreationFailureException
     */
    #[ArrayShape(['validationErrors'])]
    public function processNewDevice(NewDeviceDTO $newDeviceDTO): array;

    /**
     * @throws ORMException
     */
    public function saveDevice(Devices $device, bool $sendUpdateToDevice = false): bool;
}
