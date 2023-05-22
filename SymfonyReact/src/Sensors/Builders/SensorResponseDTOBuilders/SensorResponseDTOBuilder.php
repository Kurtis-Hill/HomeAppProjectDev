<?php

namespace App\Sensors\Builders\SensorResponseDTOBuilders;

use App\Common\Services\RequestTypeEnum;
use App\Devices\Builders\DeviceResponse\DeviceResponseDTOBuilder;
use App\Sensors\Builders\SensorReadingTypeResponseBuilders\SensorReadingTypeDTOResponseBuilder;
use App\Sensors\Builders\SensorTypeDTOBuilders\SensorTypeResponseDTOBuilder;
use App\Sensors\Builders\SensorUpdateBuilders\SensorUpdateDTOBuilder;
use App\Sensors\DTO\Internal\Sensor\UpdateSensorDTO;
use App\Sensors\DTO\Response\SensorReadingTypeResponse\SensorReadingTypeResponseDTOInterface;
use App\Sensors\DTO\Response\SensorResponse\SensorResponseDTO;
use App\Sensors\Entity\Sensor;
use App\Sensors\Voters\SensorVoter;
use App\User\Builders\User\UserResponseBuilder;
use Symfony\Bundle\SecurityBundle\Security;

class SensorResponseDTOBuilder
{
    private SensorReadingTypeDTOResponseBuilder $sensorReadingTypeDTOResponseBuilder;

    private Security $security;

    public function __construct(
        SensorReadingTypeDTOResponseBuilder $sensorReadingTypeDTOResponseBuilder,
        Security $security,
    ) {
        $this->sensorReadingTypeDTOResponseBuilder = $sensorReadingTypeDTOResponseBuilder;
        $this->security = $security;
    }

    public function buildFullSensorResponseDTOWithPermissions(Sensor $sensor, array $groups): SensorResponseDTO
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
            $sensorReadingTypeDTO = $this->sensorReadingTypeDTOResponseBuilder->buildSensorReadingTypeResponseDTOs($sensor);
        }

        return self::buildSensorResponseDTO(
            $sensor,
            $sensorReadingTypeDTO ?? [],
            $this->security->isGranted(
                SensorVoter::UPDATE_SENSOR,
                SensorUpdateDTOBuilder::buildSensorUpdateDTO(
                    $sensor,
                    $sensor->getSensorName(),
                    $sensor->getDevice()
                )
            ),
            $this->security->isGranted(SensorVoter::DELETE_SENSOR, $sensor),

        );
    }

    /**
     * @param Sensor $sensor
     * @param SensorReadingTypeResponseDTOInterface[] $sensorReadingTypeDTO
     * @param bool|null $canEdit
     * @param bool|null $canDelete
     * @return SensorResponseDTO
     */
    public static function buildSensorResponseDTO(
        Sensor $sensor,
        array $sensorReadingTypeDTO = [],
        ?bool $canEdit = null,
        ?bool $canDelete = null,
    ): SensorResponseDTO {
        return new SensorResponseDTO(
            $sensor->getSensorID(),
            UserResponseBuilder::buildUserResponseDTO($sensor->getCreatedBy()),
            $sensor->getSensorName(),
            DeviceResponseDTOBuilder::buildDeviceResponseDTO($sensor->getDevice()),
            SensorTypeResponseDTOBuilder::buildFullSensorTypeResponseDTO($sensor->getSensorTypeObject()),
            $sensorReadingTypeDTO,
            $canEdit,
            $canDelete,
        );
    }
}
