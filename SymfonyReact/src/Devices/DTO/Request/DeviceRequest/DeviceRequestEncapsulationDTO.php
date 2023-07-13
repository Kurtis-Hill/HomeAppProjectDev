<?php

namespace App\Devices\DTO\Request\DeviceRequest;

readonly class DeviceRequestEncapsulationDTO
{
    public function __construct(
        private string $fullSensorUrl,
        private RequestSensorCurrentReadingUpdateRequestDTO $requestSensorCurrentReadingUpdateRequestDTO,
    ) {}

    public function getFullSensorUrl(): string
    {
        return $this->fullSensorUrl;
    }

    public function getDeviceRequestDTO(): DeviceRequestDTOInterface
    {
        return $this->requestSensorCurrentReadingUpdateRequestDTO;
    }
}
