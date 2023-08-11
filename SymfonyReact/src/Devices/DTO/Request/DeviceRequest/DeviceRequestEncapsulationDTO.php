<?php

namespace App\Devices\DTO\Request\DeviceRequest;

readonly class DeviceRequestEncapsulationDTO
{
    public function __construct(
        private string $fullSensorUrl,
        private DeviceRequestDTOInterface $deviceRequestDTO,
    ) {}

    public function getFullSensorUrl(): string
    {
        return $this->fullSensorUrl;
    }

    public function getDeviceRequestDTO(): DeviceRequestDTOInterface
    {
        return $this->deviceRequestDTO;
    }
}
