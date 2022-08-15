<?php

namespace App\Devices\DeviceServices\DevicePasswordService;

use App\Devices\Entity\Devices;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

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

    public function decodeDevicePassword(Devices $device, string $password): bool
    {
        return $this->passwordEncoder->isPasswordValid($device, $password);
    }
}
