<?php

namespace App\Sensors\Builders\Response\TriggerResponseBuilder;

use App\Common\Builders\Operator\OperatorResponseDTOBuilder;
use App\Sensors\Builders\Response\SensorReadingTypeResponseBuilders\Standard\SensorReadingTypeDTOResponseBuilder;
use App\Sensors\DTO\Response\SensorReadingTypeResponse\AllSensorReadingTypeResponseDTOInterface;
use App\Sensors\DTO\Response\Trigger\SensorTriggerResponseDTO;
use App\Sensors\Entity\SensorTrigger;
use App\Sensors\Exceptions\UserNotAllowedException;
use App\Sensors\SensorServices\SensorReadingTypeFetcher;
use App\User\Builders\User\UserResponseBuilder;
use App\User\Entity\User;

readonly class SensorTriggerResponseDTOBuilder
{
    public function __construct(
        private SensorReadingTypeFetcher $readingTypeFetcher,
        private SensorReadingTypeDTOResponseBuilder $sensorReadingTypeDTOResponseBuilder,
    ) {
    }

    /**
     * @throws UserNotAllowedException
     */
    public static function buildSensorTriggerResponseDTO(
        SensorTrigger $sensorTrigger,
        ?AllSensorReadingTypeResponseDTOInterface $baseReadingTypeThatTriggersDTO,
        ?AllSensorReadingTypeResponseDTOInterface $baseReadingTypeThatIsTriggeredDTO,
    ): SensorTriggerResponseDTO {
        $user = $sensorTrigger->getCreatedBy();
        if (!$user instanceof User) {
            throw new UserNotAllowedException('User not allowed');
        }

        return new SensorTriggerResponseDTO(
            $sensorTrigger->getSensorTriggerID(),
            OperatorResponseDTOBuilder::buildOperatorResponseDTO($sensorTrigger->getOperator()),
            TriggerTypeResponseBuilder::buildTriggerTypeResponseDTO($sensorTrigger->getTriggerType()),
            $sensorTrigger->getValueThatTriggers(),
            UserResponseBuilder::buildUserResponseDTO($user),
            $sensorTrigger->getStartTime(),
            $sensorTrigger->getEndTime(),
            $sensorTrigger->getCreatedAt()->format('d-m-Y H:i:s'),
            $sensorTrigger->getUpdatedAt()->format('d-m-Y H:i:s'),
            $sensorTrigger->getMonday(),
            $sensorTrigger->getTuesday(),
            $sensorTrigger->getWednesday(),
            $sensorTrigger->getThursday(),
            $sensorTrigger->getFriday(),
            $sensorTrigger->getSaturday(),
            $sensorTrigger->getSunday(),
            $baseReadingTypeThatTriggersDTO,
            $baseReadingTypeThatIsTriggeredDTO,
        );
    }

    /**
     * @throws UserNotAllowedException
     */
    public function buildFullSensorTriggerResponseDTO(SensorTrigger $sensorTrigger): SensorTriggerResponseDTO
    {
        $baseReadingTypeToTriggerID = $sensorTrigger->getBaseReadingTypeToTriggers() !== null
            ? $this->readingTypeFetcher->fetchReadingTypeBasedOnBaseReadingType($sensorTrigger->getBaseReadingTypeToTriggers()->getBaseReadingTypeID())
            : null;

        if ($baseReadingTypeToTriggerID !== null) {
            $baseReadingTypeToTrigger = $this->sensorReadingTypeDTOResponseBuilder->buildSensorReadingTypeResponseDTO($baseReadingTypeToTriggerID);
        }

        $baseReadingTypeThatTriggersID = $sensorTrigger->getBaseReadingTypeThatTriggers() !== null
            ? $this->readingTypeFetcher->fetchReadingTypeBasedOnBaseReadingType($sensorTrigger->getBaseReadingTypeThatTriggers()?->getBaseReadingTypeID())
            : null;
        if ($baseReadingTypeThatTriggersID !== null) {
            $baseReadingTypeThatTriggers = $this->sensorReadingTypeDTOResponseBuilder->buildSensorReadingTypeResponseDTO($baseReadingTypeThatTriggersID);
        }

        return self::buildSensorTriggerResponseDTO(
            $sensorTrigger,
            $baseReadingTypeThatTriggers ?? null,
            $baseReadingTypeToTrigger ?? null,
        );
    }
}
