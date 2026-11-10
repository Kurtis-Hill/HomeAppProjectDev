<?php
declare(strict_types=1);

namespace App\DTOs\Device\Request;

use Symfony\Component\Validator\Constraints as Assert;

class DeviceIpUpdateRequestDTO
{
    #[
        Assert\NotBlank(message: 'ipAddress should not be blank'),
        Assert\Ip(message: '"{{ value }}" is not a valid IP address'),
    ]
    private ?string $ipAddress = null;

    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    public function setIpAddress(?string $ipAddress): void
    {
        $this->ipAddress = $ipAddress;
    }
}
