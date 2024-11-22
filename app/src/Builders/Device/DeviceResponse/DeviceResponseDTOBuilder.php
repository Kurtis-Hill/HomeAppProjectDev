<?php
declare(strict_types=1);

namespace App\Builders\Device\DeviceResponse;

use App\Builders\Device\DeviceUpdate\DeviceDTOBuilder;
use App\Builders\Sensor\Response\SensorResponseDTOBuilders\SensorResponseDTOBuilder;
use App\Builders\User\GroupName\GroupResponseDTOBuilder;
use App\Builders\User\RoomDTOBuilder\RoomResponseDTOBuilder;
use App\Builders\User\User\UserResponseBuilder;
use App\DTOs\Device\Request\DeviceUpdateRequestDTO;
use App\DTOs\Device\Response\DeviceResponseDTO;
use App\Entity\Device\Devices;
use App\Entity\Sensor\Sensor;
use App\Exceptions\Sensor\ReadingTypeNotExpectedException;
use App\Exceptions\User\GroupExceptions\GroupNotFoundException;
use App\Exceptions\User\RoomsExceptions\RoomNotFoundException;
use App\Repository\Sensor\Sensors\SensorRepositoryInterface;
use App\Services\Request\RequestTypeEnum;
use App\Voters\DeviceVoter;
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

    /**
     * @throws GroupNotFoundException
     * @throws RoomNotFoundException
     */
    public function buildDeviceResponseDTOWithDevicePermissions(
        Devices $device,
        array $sensorReadingTypeDTOs = [],
    ): DeviceResponseDTO {
        return self::buildDeviceResponseDTO(
            $device,
            $sensorReadingTypeDTOs,
            $this->security->isGranted(
                DeviceVoter::UPDATE_DEVICE,
                $this->deviceDTOBuilder->buildUpdateDeviceInternalDTO(
                    (new DeviceUpdateRequestDTO()),
                    $device,
                )
            ),
            $this->security->isGranted(DeviceVoter::DELETE_DEVICE, $device),
        );
    }

    /**
     * @throws GroupNotFoundException
     * @throws RoomNotFoundException
     * @throws ReadingTypeNotExpectedException
     */
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
