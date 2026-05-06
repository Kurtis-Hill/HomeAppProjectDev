<?php

namespace App\Services\Device;

use App\Entity\Device\Devices;
use App\Repository\Device\ORM\DeviceRepository;

readonly class DuplicateDeviceChecker
{
    public function __construct(
        private DeviceRepository $deviceRepository,
    ) {
    }

    public function duplicateDeviceCheck(string $deviceName, int $roomID): ?Devices
    {
        $currentUserDeviceCheck = $this->deviceRepository->findDuplicateDeviceNewDeviceCheck(
            $deviceName,
            $roomID,
        );

        if ($currentUserDeviceCheck instanceof Devices) {
            return $currentUserDeviceCheck;
        }

        return null;
    }
}
