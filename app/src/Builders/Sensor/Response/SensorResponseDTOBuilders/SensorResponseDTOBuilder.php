<?php

namespace App\Builders\Sensor\Response\SensorResponseDTOBuilders;

use App\Builders\Device\DeviceResponse\DeviceResponseDTOBuilder;
use App\Builders\Sensor\Request\SensorUpdateBuilders\SensorUpdateDTOBuilder;
use App\Builders\Sensor\Response\SensorReadingTypeResponseBuilders\Standard\SensorReadingTypeDTOResponseBuilder;
use App\Builders\Sensor\Response\SensorTypeDTOBuilders\SensorTypeResponseDTOBuilder;
use App\Builders\User\User\UserResponseBuilder;
use App\Builders\UserInterface\CardUpdateDTOBuilders\CardResponseDTOBuilder;
use App\DTOs\Sensor\Response\SensorResponse\SensorResponseDTO;
use App\Entity\Sensor\Sensor;
use App\Entity\User\User;
use App\Entity\UserInterface\Card\CardView;
use App\Repository\UserInterface\ORM\CardRepositories\CardViewRepository;
use App\Services\Request\RequestTypeEnum;
use App\Voters\SensorVoter;
use Symfony\Bundle\SecurityBundle\Security;

class SensorResponseDTOBuilder
{
    private SensorReadingTypeDTOResponseBuilder $sensorReadingTypeDTOResponseBuilder;

    private CardViewRepository $cardViewRepository;

    private Security $security;

    public function __construct(
        SensorReadingTypeDTOResponseBuilder $sensorReadingTypeDTOResponseBuilder,
        CardViewRepository $cardViewRepository,
        Security $security,
    ) {
        $this->sensorReadingTypeDTOResponseBuilder = $sensorReadingTypeDTOResponseBuilder;
        $this->cardViewRepository = $cardViewRepository;
        $this->security = $security;
    }

    public function buildFullSensorResponseDTOWithPermissions(Sensor $sensor, array $groups = []): SensorResponseDTO
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
            $sensorReadingTypeDTOs = $this->sensorReadingTypeDTOResponseBuilder->buildSensorReadingTypeResponseDTOs($sensor);

            $user = $this->security->getUser();
            if ($user instanceof User) {
                $cardView = $this->cardViewRepository->findOneBy(['userID' => $user, 'sensor' => $sensor]);
            }
        }

        return self::buildSensorResponseDTO(
            $sensor,
            $sensorReadingTypeDTOs ?? [],
            $this->security->isGranted(
                SensorVoter::UPDATE_SENSOR,
                SensorUpdateDTOBuilder::buildSensorUpdateDTO(
                    $sensor,
                    $sensor->getSensorName(),
                    $sensor->getDevice(),
                    $sensor->getPinNumber(),
                )
            ),
            $this->security->isGranted(SensorVoter::DELETE_SENSOR, $sensor),
            $cardView ?? null,

        );
    }

    /**
     * @param \App\Entity\Sensor\Sensor $sensor
     * @param array $sensorReadingTypeDTOs
     * @param bool|null $canEdit
     * @param bool|null $canDelete
     * @param CardView|null $cardView
     * @return \App\DTOs\Sensor\Response\SensorResponse\SensorResponseDTO
     */
    public static function buildSensorResponseDTO(
        Sensor $sensor,
        array $sensorReadingTypeDTOs = [],
        ?bool $canEdit = null,
        ?bool $canDelete = null,
        ?CardView $cardView = null,
    ): SensorResponseDTO {
        return new SensorResponseDTO(
            $sensor->getSensorID(),
            UserResponseBuilder::buildUserResponseDTO($sensor->getCreatedBy()),
            $sensor->getSensorName(),
            DeviceResponseDTOBuilder::buildDeviceResponseDTO($sensor->getDevice()),
            SensorTypeResponseDTOBuilder::buildFullSensorTypeResponseDTO($sensor->getSensorTypeObject()),
            $sensor->getPinNumber(),
            $sensor->getReadingInterval(),
            $sensorReadingTypeDTOs,
            $canEdit,
            $canDelete,
            $cardView !== null ? CardResponseDTOBuilder::buildCardResponseDTO($cardView) : null,
        );
    }
}
