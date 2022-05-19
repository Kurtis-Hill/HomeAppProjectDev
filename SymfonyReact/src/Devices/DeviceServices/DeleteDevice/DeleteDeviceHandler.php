<?php

namespace App\Devices\DeviceServices\DeleteDevice;

use App\Devices\Entity\Devices;
use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use Doctrine\ORM\Exception\ORMException;

class DeleteDeviceHandler implements DeleteDeviceHandlerInterface
{
    private DeviceRepositoryInterface $deviceRepository;

    public function __construct(DeviceRepositoryInterface $deviceRepository)
    {
        $this->deviceRepository = $deviceRepository;
    }

    public function deleteDevice(Devices $devices): bool
    {
        try {
            $this->deviceRepository->remove($devices);
            $this->deviceRepository->flush();
        } catch (ORMException) {
            return false;
        }

        return true;
    }
}
