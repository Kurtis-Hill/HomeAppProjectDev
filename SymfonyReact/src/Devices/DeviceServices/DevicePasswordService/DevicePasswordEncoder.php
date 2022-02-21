<?php

namespace App\Devices\DeviceServices\DevicePasswordService;

use App\Devices\Entity\Devices;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class DevicePasswordEncoder implements DevicePasswordEncoderInterface
{
    private UserPasswordEncoderInterface $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function encodeDevicePassword(Devices $newDevice): void
    {
        $newDevice->setPassword(
            $this->passwordEncoder->encodePassword(
                $newDevice,
                $newDevice->getDeviceSecret()
            )
        );
    }
}
