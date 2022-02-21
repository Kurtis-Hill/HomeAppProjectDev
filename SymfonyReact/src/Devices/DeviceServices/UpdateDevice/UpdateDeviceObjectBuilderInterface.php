<?php

namespace App\Devices\DeviceServices\UpdateDevice;

use App\Devices\DTO\Request\DeviceRequestDTOInterface;
use App\Devices\DTO\Response\DeviceUpdateResponseDTO;
use App\Devices\DTO\UpdateDeviceDTO;
use App\Devices\Entity\Devices;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\ORMException;

interface UpdateDeviceObjectBuilderInterface
{
    public function validateDeviceRequestObject(DeviceRequestDTOInterface $deviceUpdateRequestDTO): array;

    /**
     * @throws NonUniqueResultException
     * @throws ORMException
     */
    public function findDeviceToUpdate(int $deviceID): ?Devices;

    public function updateDeviceAndValidate(UpdateDeviceDTO $deviceUpdateRequestDTO): array;

    public function saveNewDevice(Devices $device): bool;

    public function buildSensorSuccessResponseDTO(Devices $updatedDevice): DeviceUpdateResponseDTO;
}
