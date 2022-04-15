<?php

namespace App\Devices\Builders\DeviceUpdate;

use App\Devices\DeviceServices\DevicePasswordService\DevicePasswordEncoderInterface;
use App\Devices\DTO\Response\DeviceDTO;
use App\Devices\Entity\Devices;

class DeviceDTOBuilder
{
    private static DevicePasswordEncoderInterface $devicePasswordEncoder;

    public function __construct($he)
    {
        self::$devicePasswordEncoder = $he;
    }

    public static function buildDeviceDTO(Devices $device, bool $showPassword = false): DeviceDTO
    {
        self::$devicePasswordEncoder->encodeDevicePassword();

        return new DeviceDTO(
            $device->getDeviceNameID(),
            $device->getDeviceName(),
            $device->getGroupNameObject()->getGroupNameID(),
            $device->getRoomObject()->getRoomID(),
            $device->getCreatedBy()->getUsername(),
            $showPassword === false ? null : $device->getPassword(),
            $device->getIpAddress(),
            $device->getExternalIpAddress(),
        );
    }
}
