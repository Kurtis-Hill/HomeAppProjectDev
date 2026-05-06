<?php

namespace App\Builders\Sensor\Response\TriggerResponseBuilder;

use App\Builders\Operator\OperatorResponseDTOBuilder;
use App\Builders\Sensor\Response\SensorReadingTypeResponseBuilders\Standard\SensorReadingTypeDTOResponseBuilder;
use App\Builders\User\User\UserResponseBuilder;
use App\DTOs\Common\Response\DaysResponseDTO;
use App\DTOs\Sensor\Response\SensorReadingTypeResponse\AllSensorReadingTypeResponseDTOInterface;
use App\DTOs\Sensor\Response\Trigger\SensorTriggerResponseDTO;
use App\Entity\Sensor\SensorTrigger;
use App\Entity\User\User;
use App\Exceptions\Sensor\UserNotAllowedException;
use App\Services\Sensor\SensorReadingTypeFetcher;

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
            strlen((string)$sensorTrigger->getStartTime()) === 3 ? sprintf('0%d',$sensorTrigger->getStartTime()) : $sensorTrigger->getStartTime(),
            strlen((string)$sensorTrigger->getEndTime()) === 3 ? sprintf('0%d',$sensorTrigger->getEndTime()) : $sensorTrigger->getEndTime(),
            $sensorTrigger->getCreatedAt()->format('d-m-Y H:i:s'),
            $sensorTrigger->getUpdatedAt()->format('d-m-Y H:i:s'),
            new DaysResponseDTO(
                $sensorTrigger->getMonday(),
                $sensorTrigger->getTuesday(),
                $sensorTrigger->getWednesday(),
                $sensorTrigger->getThursday(),
                $sensorTrigger->getFriday(),
                $sensorTrigger->getSaturday(),
                $sensorTrigger->getSunday(),
            ),
            $sensorTrigger->getOverride(),
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
