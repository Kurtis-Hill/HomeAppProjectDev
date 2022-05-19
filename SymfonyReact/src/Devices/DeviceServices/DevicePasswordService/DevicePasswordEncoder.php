<?php

namespace App\Devices\DeviceServices\DevicePasswordService;

use App\Devices\Entity\Devices;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class DevicePasswordEncoder implements DevicePasswordEncoderInterface
{
    private UserPasswordHasherInterface $passwordEncoder;

    public function __construct(UserPasswordHasherInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function encodeDevicePassword(Devices $newDevice): void
    {
        $newDevice->setPassword(
            $this->passwordEncoder->hashPassword(
                $newDevice,
                $newDevice->getDeviceSecret()
            )
        );
    }

    public function decodeDevicePassword(Devices $device): void
    {
//        $device->setPassword(
//            $this->passwordEncoder->(
//                $device,
//                $device->getDeviceSecret()
//            )
//        );
    }
}
