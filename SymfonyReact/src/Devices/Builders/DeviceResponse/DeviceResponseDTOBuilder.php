<?php

namespace App\Devices\Builders\DeviceResponse;

use App\Common\Services\RequestTypeEnum;
use App\Devices\Builders\DeviceUpdate\DeviceDTOBuilder;
use App\Devices\DTO\Request\DeviceUpdateRequestDTO;
use App\Devices\DTO\Response\DeviceResponseDTO;
use App\Devices\Entity\Devices;
use App\Devices\Voters\DeviceVoter;
use App\Sensors\Builders\SensorResponseDTOBuilders\SensorResponseDTOBuilder;
use App\Sensors\Repository\Sensors\SensorRepositoryInterface;
use App\Sensors\SensorServices\GetSensorReadingTypeHandler;
use App\User\Builders\GroupName\GroupNameResponseDTOBuilder;
use App\User\Builders\RoomDTOBuilder\RoomResponseDTOBuilder;
use Symfony\Bundle\SecurityBundle\Security;

class DeviceResponseDTOBuilder
{
    private SensorRepositoryInterface $sensorRepository;

    private SensorResponseDTOBuilder $sensorResponseDTOBuilder;

    private Security $security;

    public function __construct(
        SensorRepositoryInterface $sensorRepository,
        SensorResponseDTOBuilder $getSensorReadingTypeHandler,
        DeviceVoter $deviceVoter,
        Security $security,
    ) {
        $this->sensorRepository = $sensorRepository;
        $this->sensorResponseDTOBuilder = $getSensorReadingTypeHandler;
        $this->security = $security;
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
//            $this->security->isGranted(DeviceVoter::UPDATE_DEVICE,
//                    DeviceDTOBuilder::buildUpdateDeviceInternalDTO(
//                        new DeviceUpdateRequestDTO(),
//                        $device,
//                        $device->getRoomObject(),
//                        $device->getGroupObject(),
//                    )
//            ),
//            $this->security->isGranted(DeviceVoter::DELETE_DEVICE, $device),
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
