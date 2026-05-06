<?php
declare(strict_types=1);

namespace App\Services\Device\DevicePasswordService;

use App\Entity\Device\Devices;
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
}
