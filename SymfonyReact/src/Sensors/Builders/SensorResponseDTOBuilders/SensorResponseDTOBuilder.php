<?php

namespace App\Sensors\Builders\SensorResponseDTOBuilders;

use App\Common\Services\RequestTypeEnum;
use App\Devices\Builders\DeviceResponse\DeviceResponseDTOBuilder;
use App\Sensors\Builders\SensorTypeDTOBuilders\SensorTypeResponseDTOBuilder;
use App\Sensors\DTO\Response\SensorReadingTypeResponse\SensorReadingTypeResponseDTOInterface;
use App\Sensors\DTO\Response\SensorResponse\SensorResponseDTO;
use App\Sensors\Entity\Sensor;
use App\Sensors\SensorServices\GetSensorReadingTypeHandler;
use App\User\Builders\User\UserResponseBuilder;

class SensorResponseDTOBuilder
{
    private GetSensorReadingTypeHandler $getSensorReadingTypeHandler;

    public function __construct(GetSensorReadingTypeHandler $getSensorReadingTypeHandler)
    {
        $this->getSensorReadingTypeHandler = $getSensorReadingTypeHandler;
    }

    /**
     * @param Sensor $sensor
     * @param SensorReadingTypeResponseDTOInterface[] $sensorReadingTypeDTO
     * @return SensorResponseDTO
     */
    public static function buildSensorResponseDTO(Sensor $sensor, array $sensorReadingTypeDTO = []): SensorResponseDTO
    {
        return new SensorResponseDTO(
            $sensor->getSensorID(),
            UserResponseBuilder::buildUserResponseDTO($sensor->getCreatedBy()),
            $sensor->getSensorName(),
            DeviceResponseDTOBuilder::buildDeviceResponseDTO($sensor->getDevice()),
            SensorTypeResponseDTOBuilder::buildFullSensorTypeResponseDTO($sensor->getSensorTypeObject()),
            $sensorReadingTypeDTO,
        );
    }

    public function buildFullSensorResponseDTO(Sensor $sensor, array $groups): SensorResponseDTO
    {
        if (
            !empty($groups)
            && in_array(
                $groups,
                [
                    [RequestTypeEnum::FULL->value],
                    [RequestTypeEnum::SENSITIVE_FULL->value],
                ],
                true
            )) {
            $sensorReadingTypeDTO = $this->getSensorReadingTypeHandler->handleSensorReadingTypeDTOCreation($sensor);
        }

        return self::buildSensorResponseDTO(
            $sensor,
            $sensorReadingTypeDTO ?? [],
        );
    }
}
