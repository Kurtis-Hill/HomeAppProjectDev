<?php

namespace App\Devices\Builders\DeviceResponse;

use App\Common\Services\RequestTypeEnum;
use App\Devices\DTO\Response\DeviceResponseDTO;
use App\Devices\Entity\Devices;
use App\Sensors\Builders\SensorResponseDTOBuilders\SensorResponseDTOBuilder;
use App\Sensors\Repository\Sensors\SensorRepositoryInterface;
use App\Sensors\SensorServices\GetSensorReadingTypeHandler;
use App\User\Builders\GroupName\GroupNameResponseDTOBuilder;
use App\User\Builders\RoomDTOBuilder\RoomResponseDTOBuilder;

class DeviceResponseDTOBuilder
{
    private SensorRepositoryInterface $sensorRepository;

    private SensorResponseDTOBuilder $sensorResponseDTOBuilder;

    public function __construct(
        SensorRepositoryInterface $sensorRepository,
        SensorResponseDTOBuilder $getSensorReadingTypeHandler,
    ) {
        $this->sensorRepository = $sensorRepository;
        $this->sensorResponseDTOBuilder = $getSensorReadingTypeHandler;
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
                    $sensorResponseDTOs[] = $this->sensorResponseDTOBuilder->buildFullSensorResponseDTO($sensor, [RequestTypeEnum::FULL->value]);
                }
            }
        }

        return self::buildDeviceResponseDTO(
            $device,
            $sensorResponseDTOs ?? [],
        );
    }
}
