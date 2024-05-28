<?php
declare(strict_types=1);

namespace App\Authentication\DTOs\Request;

use Symfony\Component\Validator\Constraints as Assert;

class DeviceAuthenticationIPRequestDTO
{
    #[Assert\Ip]
    private mixed $ipAddress = null;

    #[Assert\Ip]
    private mixed $externalIpAddress = null;

    public function getIpAddress(): mixed
    {
        return $this->ipAddress;
    }

    public function getExternalIpAddress(): mixed
    {
        return $this->externalIpAddress;
    }

    public function setIpAddress(mixed $ipAddress): void
    {
        $this->ipAddress = $ipAddress;
    }

    public function setExternalIpAddress(mixed $externalIpAddress): void
    {
        $this->externalIpAddress = $externalIpAddress;
    }
}
