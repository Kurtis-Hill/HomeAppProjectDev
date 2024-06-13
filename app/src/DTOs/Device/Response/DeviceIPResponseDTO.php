<?php
declare(strict_types=1);

namespace App\DTOs\Device\Response;

readonly class DeviceIPResponseDTO
{
    public function __construct(
        private string $ipAddress,
    ) {}

    public function getipAddress(): string
    {
        return $this->ipAddress;
    }
}
