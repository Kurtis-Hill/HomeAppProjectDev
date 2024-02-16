<?php

namespace App\Sensors\Builders\Request\SensorUpdateBuilders;

use App\Devices\Entity\Devices;
use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use App\Sensors\DTO\Internal\Sensor\UpdateSensorDTO;
use App\Sensors\DTO\Request\SensorUpdateDTO\UpdateSensorDetailsRequestDTO;
use App\Sensors\Entity\Sensor;
use App\Sensors\Exceptions\DeviceNotFoundException;

readonly class SensorUpdateDTOBuilder
{
    public function __construct(
        private DeviceRepositoryInterface $deviceRepository
    ) {}

    public static function buildSensorUpdateDTO(
        Sensor $sensor,
        ?string $sensorName = null,
        ?Devices $deviceID = null,
        ?int $pinNumber = null,
        ?int $readingInterval = null,
    ): UpdateSensorDTO {
        return new UpdateSensorDTO(
            $sensor,
            $sensorName,
            $deviceID,
            $pinNumber,
            $readingInterval,
        );
    }

    /**
     * @throws DeviceNotFoundException
     */
    public function buildSensorUpdateDTOFromRequestDTO(UpdateSensorDetailsRequestDTO $sensorUpdateRequestDTO, Sensor $sensor): UpdateSensorDTO
    {
        if ($sensorUpdateRequestDTO->getDeviceID() !== null) {
            $proposedDevice = $this->deviceRepository->find($sensorUpdateRequestDTO->getDeviceID());
            if ($proposedDevice === null) {
                throw new DeviceNotFoundException(
                    sprintf(
                        DeviceNotFoundException::DEVICE_NOT_FOUND_FOR_ID,
                        $sensorUpdateRequestDTO->getDeviceID()
                    )
                );
            }
        }

        return self::buildSensorUpdateDTO(
            $sensor,
            $sensorUpdateRequestDTO->getSensorName(),
            $proposedDevice ?? null,
            $sensorUpdateRequestDTO->getPinNumber(),
            $sensorUpdateRequestDTO->getReadingInterval(),
        );
    }
}
