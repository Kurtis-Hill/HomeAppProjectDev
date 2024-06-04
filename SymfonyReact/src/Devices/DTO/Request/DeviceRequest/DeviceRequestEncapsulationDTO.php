<?php
declare(strict_types=1);

namespace App\Devices\DTO\Request\DeviceRequest;

readonly class DeviceRequestEncapsulationDTO
{
    public function __construct(
        private string $fullDeviceUrl,
        private DeviceRequestDTOInterface $deviceRequestDTO,
    ) {}

    public function getFullDeviceUrl(): string
    {
        return $this->fullDeviceUrl;
    }

    public function getDeviceRequestDTO(): DeviceRequestDTOInterface
    {
        return $this->deviceRequestDTO;
    }
}
