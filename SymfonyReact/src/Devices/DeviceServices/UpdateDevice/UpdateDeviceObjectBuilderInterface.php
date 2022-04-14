<?php

namespace App\Devices\DeviceServices\UpdateDevice;

use App\Devices\DTO\Internal\UpdateDeviceDTO;
use App\Devices\DTO\Request\DeviceRequestDTOInterface;
use App\Devices\DTO\Response\DeviceUpdateResponseDTO;
use App\Devices\Entity\Devices;

interface UpdateDeviceObjectBuilderInterface
{
    public function validateDeviceRequestObject(DeviceRequestDTOInterface $deviceUpdateRequestDTO): array;

    public function updateDeviceAndValidate(UpdateDeviceDTO $deviceUpdateRequestDTO): array;

    public function saveNewDevice(Devices $device): bool;

    public function buildSensorSuccessResponseDTO(Devices $updatedDevice): DeviceUpdateResponseDTO;
}
