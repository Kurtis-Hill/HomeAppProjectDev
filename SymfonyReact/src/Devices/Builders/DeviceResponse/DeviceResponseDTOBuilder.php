<?php

namespace App\Devices\Builders\DeviceResponse;

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

    public static function buildDeviceResponseDTO(
        Devices $device,
        array $sensorReadingTypeDTOs = [],
    ): DeviceResponseDTO {
        return new DeviceResponseDTO(
            $device->getDeviceID(),
            $device->getDeviceName(),
            $device->getDeviceSecret(),
            GroupNameResponseDTOBuilder::buildGroupNameResponseDTO($device->getGroupObject()),
            RoomResponseDTOBuilder::buildRoomResponseDTO($device->getRoomObject()),
            $device->getIpAddress(),
            $device->getExternalIpAddress(),
            $device->getRoles(),
            $sensorReadingTypeDTOs,
        );
    }

    public function buildFullDeviceResponseDTO(Devices $device, bool $includeSensors = false): DeviceResponseDTO
    {
        if ($includeSensors === true) {
            $deviceSensors = $this->sensorRepository->findSensorObjectsByDeviceID($device->getDeviceID());
            if (!empty($deviceSensors)) {
                foreach ($deviceSensors as $sensor) {
                    $sensorReadingTypeDTOs[] = $this->getSensorReadingTypeHandler->handleSensorReadingTypeDTOCreating($sensor);
                }
            }
        }

        return self::buildDeviceResponseDTO(
            $device,
            $sensorReadingTypeDTOs ?? [],
        );
    }
}
