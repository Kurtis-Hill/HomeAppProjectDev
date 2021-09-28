<?php

namespace App\Devices\DeviceServices\NewDevice;

use App\Entity\Devices\Devices;
use Symfony\Component\Form\FormInterface;

interface NewDeviceServiceInterface
{
    public function handleNewDeviceSubmission(array $deviceData): ?Devices;
}
