<?php
declare(strict_types=1);

namespace App\DTOs\Device\Response;

readonly class DeviceIPResponseDTO
{
    public function __construct(
        public string $ipAddress,
    ) {}
}
