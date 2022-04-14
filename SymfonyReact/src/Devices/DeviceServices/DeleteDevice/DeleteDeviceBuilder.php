<?php

namespace App\Devices\DeviceServices\DeleteDevice;

use App\Devices\DeviceServices\AbstractESPDeviceBuilder;
use App\Devices\Entity\Devices;
use Doctrine\ORM\ORMException;

class DeleteDeviceBuilder extends AbstractESPDeviceBuilder implements DeleteDeviceBuilderInterface
{
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
