<?php

namespace App\Devices\Builders\DeviceResponse;

use App\Devices\DTO\Response\DeviceFullDetailsResponseDTO;
use App\Devices\DTO\Response\DeviceResponseDTO;
use App\Devices\Entity\Devices;
use App\Sensors\Repository\Sensors\SensorRepositoryInterface;
use App\Sensors\SensorServices\GetSensorReadingTypeHandler;
use App\User\Builders\GroupName\GroupNameResponseDTOBuilder;
use App\User\Builders\RoomDTOBuilder\RoomResponseDTOBuilder;

class DeviceResponseDTOBuilder
{
    private SensorRepositoryInterface $sensorRepository;

    private GetSensorReadingTypeHandler $getSensorReadingTypeHandler;

    public function __construct(
        SensorRepositoryInterface $sensorRepository,
        GetSensorReadingTypeHandler $getSensorReadingTypeHandler,
    ) {
        $this->sensorRepository = $sensorRepository;
        $this->getSensorReadingTypeHandler = $getSensorReadingTypeHandler;
    }

    public static function buildDeviceIDResponseDTO(Devices $device, bool $showPassword = false): DeviceResponseDTO
    {
        return new DeviceResponseDTO(
            $device->getDeviceID(),
            $device->getDeviceName(),
            $device->getGroupNameObject()->getGroupNameID(),
            $device->getRoomObject()->getRoomID(),
            $device->getCreatedBy()->getUsername(),
            $showPassword === false ? null : $device->getDeviceSecret(),
            $device->getIpAddress(),
            $device->getExternalIpAddress(),
            $device->getRoles(),
        );
    }

    public static function buildDeletedDeviceResponseDTO(Devices $device, bool $showPassword = false): DeviceResponseDTO
    {
        return new DeviceResponseDTO(
            null,
            $device->getDeviceName(),
            $device->getGroupNameObject()->getGroupNameID(),
            $device->getRoomObject()->getRoomID(),
            $device->getCreatedBy()->getUsername(),
            $showPassword === false ? null : $device->getDeviceSecret(),
            $device->getIpAddress(),
            $device->getExternalIpAddress(),
            $device->getRoles(),
        );
    }

    public static function buildDeviceOnlyResponseDTO(
        Devices $device,
        array $sensorReadingTypeDTOs = [],
    ): DeviceFullDetailsResponseDTO {
        return new DeviceFullDetailsResponseDTO(
            $device->getDeviceID(),
            $device->getDeviceName(),
            $device->getDeviceSecret(),
            GroupNameResponseDTOBuilder::buildGroupNameResponseDTO($device->getGroupNameObject()),
            RoomResponseDTOBuilder::buildRoomResponseDTO($device->getRoomObject()),
            $device->getIpAddress(),
            $device->getExternalIpAddress(),
            $device->getRoles(),
            $sensorReadingTypeDTOs,
        );
    }

    public function buildDeviceResponseDTO(Devices $device, bool $includeSensors = false): DeviceFullDetailsResponseDTO
    {
        if ($includeSensors === true) {
            $deviceSensors = $this->sensorRepository->findSensorObjectsByDeviceID($device->getDeviceID());
            if (!empty($deviceSensors)) {
                $sensorReadingTypeDTOs = [];
                foreach ($deviceSensors as $sensor) {
                    $sensorReadingTypeDTOs[] = $this->getSensorReadingTypeHandler->handleSensorReadingTypeDTOCreating($sensor);
                }
            }
        }

        return self::buildDeviceOnlyResponseDTO(
            $device,
            $sensorReadingTypeDTOs ?? [],
        );
    }
}
