<?php

namespace App\Sensors\Builders\SensorResponseDTOBuilders;

use App\Common\Services\RequestTypeEnum;
use App\Devices\Builders\DeviceResponse\DeviceResponseDTOBuilder;
use App\Sensors\Builders\SensorReadingTypeResponseBuilders\Standard\SensorReadingTypeDTOResponseBuilder;
use App\Sensors\Builders\SensorTypeDTOBuilders\SensorTypeResponseDTOBuilder;
use App\Sensors\Builders\SensorUpdateBuilders\SensorUpdateDTOBuilder;
use App\Sensors\DTO\Response\SensorResponse\SensorResponseDTO;
use App\Sensors\Entity\Sensor;
use App\Sensors\Exceptions\ReadingTypeNotExpectedException;
use App\Sensors\Voters\SensorVoter;
use App\User\Builders\User\UserResponseBuilder;
use App\User\Entity\User;
use App\UserInterface\Builders\CardUpdateDTOBuilders\CardResponseDTOBuilder;
use App\UserInterface\Entity\Card\CardView;
use App\UserInterface\Repository\ORM\CardRepositories\CardViewRepository;
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
     * @param Sensor $sensor
     * @param array $sensorReadingTypeDTOs
     * @param bool|null $canEdit
     * @param bool|null $canDelete
     * @param CardView|null $cardView
     * @return SensorResponseDTO
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
