<?php

namespace App\Sensors\Builders\TriggerResponseBuilder;

use App\Common\Builders\Operator\OperatorResponseDTOBuilder;
use App\Sensors\Builders\SensorReadingTypeResponseBuilders\Standard\SensorReadingTypeDTOResponseBuilder;
use App\Sensors\DTO\Response\SensorReadingTypeResponse\AllSensorReadingTypeResponseDTOInterface;
use App\Sensors\DTO\Response\Trigger\SensorTriggerResponseDTO;
use App\Sensors\Entity\SensorTrigger;
use App\Sensors\SensorServices\SensorReadingTypeFetcher;
use App\User\Builders\User\UserResponseBuilder;

readonly class SensorTriggerResponseDTOBuilder
{
    public function __construct(
        private SensorReadingTypeFetcher $readingTypeFetcher,
        private SensorReadingTypeDTOResponseBuilder $sensorReadingTypeDTOResponseBuilder,
    ) {
    }

    public static function buildSensorTriggerResponseDTO(
        SensorTrigger $sensorTrigger,
        ?AllSensorReadingTypeResponseDTOInterface $baseReadingTypeToTriggerDTO,
        ?AllSensorReadingTypeResponseDTOInterface $baseReadingTypeThatIsTriggeredDTO,
    ): SensorTriggerResponseDTO {
        return new SensorTriggerResponseDTO(
            $sensorTrigger->getSensorTriggerID(),
            OperatorResponseDTOBuilder::buildOperatorResponseDTO($sensorTrigger->getOperator()),
            TriggerTypeResponseBuilder::buildTriggerTypeResponseDTO($sensorTrigger->getTriggerType()),
            $sensorTrigger->getValueThatTriggers(),
            UserResponseBuilder::buildUserResponseDTO($sensorTrigger->getCreatedBy()),
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
            $baseReadingTypeToTriggerDTO,
            $baseReadingTypeThatIsTriggeredDTO,
        );
    }

    public function buildFullSensorTriggerResponseDTO(SensorTrigger $sensorTrigger): SensorTriggerResponseDTO
    {
        $baseReadingTypeToTriggerID = $this->readingTypeFetcher->fetchReadingTypeBasedOnBaseReadingType($sensorTrigger->getBaseReadingTypeToTriggerID()->getBaseReadingTypeID());
        $baseReadingTypeThatIsTriggeredID = $sensorTrigger->getBaseReadingTypeThatTriggers() !== null
            ? $this->readingTypeFetcher->fetchReadingTypeBasedOnBaseReadingType($sensorTrigger->getBaseReadingTypeThatTriggers()?->getBaseReadingTypeID())
            : null;

        $baseReadingTypeToTrigger = $this->sensorReadingTypeDTOResponseBuilder->buildSensorReadingTypeResponseDTO($baseReadingTypeToTriggerID);
        if ($baseReadingTypeThatIsTriggeredID !== null) {
            $baseReadingTypeThatIsTriggered = $this->sensorReadingTypeDTOResponseBuilder->buildSensorReadingTypeResponseDTO($baseReadingTypeThatIsTriggeredID);
        }

        return self::buildSensorTriggerResponseDTO($sensorTrigger, $baseReadingTypeToTrigger, $baseReadingTypeThatIsTriggered);
    }
}
