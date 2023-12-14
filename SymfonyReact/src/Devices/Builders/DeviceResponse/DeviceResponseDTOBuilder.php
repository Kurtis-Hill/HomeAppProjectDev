<?php

namespace App\Devices\Builders\DeviceResponse;

use App\Common\Services\RequestTypeEnum;
use App\Devices\Builders\DeviceUpdate\DeviceDTOBuilder;
use App\Devices\DTO\Request\DeviceUpdateRequestDTO;
use App\Devices\DTO\Response\DeviceResponseDTO;
use App\Devices\Entity\Devices;
use App\Devices\Voters\DeviceVoter;
use App\Sensors\Builders\SensorResponseDTOBuilders\SensorResponseDTOBuilder;
use App\Sensors\Entity\Sensor;
use App\Sensors\Repository\Sensors\SensorRepositoryInterface;
use App\User\Builders\GroupName\GroupResponseDTOBuilder;
use App\User\Builders\RoomDTOBuilder\RoomResponseDTOBuilder;
use App\User\Builders\User\UserResponseBuilder;
use Symfony\Bundle\SecurityBundle\Security;

class DeviceResponseDTOBuilder
{
    private SensorRepositoryInterface $sensorRepository;

    private SensorResponseDTOBuilder $sensorResponseDTOBuilder;

    private DeviceDTOBuilder $deviceDTOBuilder;

    private Security $security;

    public function __construct(
        SensorRepositoryInterface $sensorRepository,
        SensorResponseDTOBuilder $getSensorReadingTypeHandler,
        DeviceDTOBuilder $deviceDTOBuilder,
        Security $security,
    ) {
        $this->sensorRepository = $sensorRepository;
        $this->sensorResponseDTOBuilder = $getSensorReadingTypeHandler;
        $this->deviceDTOBuilder = $deviceDTOBuilder;
        $this->security = $security;
    }

    public function buildDeviceResponseDTOWithDevicePermissions(
        Devices $device,
        array $sensorReadingTypeDTOs = [],
    ): DeviceResponseDTO {
        return self::buildDeviceResponseDTO(
            $device,
            $sensorReadingTypeDTOs,
            $this->security->isGranted(DeviceVoter::UPDATE_DEVICE,
                $this->deviceDTOBuilder->buildUpdateDeviceInternalDTO(
                    (new DeviceUpdateRequestDTO()),
                    $device,
                )
            ),
            $this->security->isGranted(DeviceVoter::DELETE_DEVICE, $device),
        );
    }

    public function buildFullDeviceResponseDTO(Devices $device, bool $includeSensors = false): DeviceResponseDTO
    {
        if ($includeSensors === true) {
            $deviceSensors = $this->sensorRepository->findSensorObjectsByDeviceID($device->getDeviceID());
            if (!empty($deviceSensors)) {
                /** @var Sensor $sensor */
                foreach ($deviceSensors as $sensor) {
                    $sensorResponseDTOs[] = $this->sensorResponseDTOBuilder->buildFullSensorResponseDTOWithPermissions($sensor, [RequestTypeEnum::FULL->value]);
                }
            }
        }

        return $this->buildDeviceResponseDTOWithDevicePermissions(
            $device,
            $sensorResponseDTOs ?? [],
        );
    }

    public static function buildDeviceResponseDTO(
        Devices $device,
        array $sensorReadingTypeDTOs = [],
        ?bool $canUpdate = null,
        ?bool $canDelete = null,
    ): DeviceResponseDTO {
        return new DeviceResponseDTO(
            $device->getDeviceID(),
            $device->getDeviceName(),
            $device->getDeviceSecret(),
            GroupResponseDTOBuilder::buildGroupNameResponseDTO($device->getGroupObject()),
            RoomResponseDTOBuilder::buildRoomResponseDTO($device->getRoomObject()),
            $device->getIpAddress(),
            $device->getExternalIpAddress(),
            $device->getRoles(),
            $sensorReadingTypeDTOs,
            UserResponseBuilder::buildUserResponseDTO($device->getCreatedBy()),
            $canUpdate,
            $canDelete,
        );
    }
}
