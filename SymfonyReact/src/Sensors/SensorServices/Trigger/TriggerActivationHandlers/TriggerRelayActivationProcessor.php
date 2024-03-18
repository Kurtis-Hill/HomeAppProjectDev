<?php

namespace App\Sensors\SensorServices\Trigger\TriggerActivationHandlers;

use App\Common\Entity\TriggerType;
use App\Sensors\Builders\Internal\AMPQMessages\CurrentReadingDTOBuilders\BoolCurrentReadingUpdateDTOBuilder;
use App\Sensors\Builders\Internal\AMPQMessages\CurrentReadingDTOBuilders\UpdateSensorCurrentReadingTransportDTOBuilder;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Relay;
use App\Sensors\Entity\SensorTrigger;
use App\Sensors\Exceptions\BaseReadingTypeNotFoundException;
use App\Sensors\Repository\SensorReadingType\ORM\BoolReadingBaseSensorRepository;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;

readonly class TriggerRelayActivationProcessor implements TriggerProcessorInterface
{
    public function __construct(
        private UpdateSensorCurrentReadingTransportDTOBuilder $updateSensorCurrentReadingDTOBuilder,
        private ProducerInterface $sendCurrentReadingAMQPProducer,
        private BoolReadingBaseSensorRepository $boolReadingBaseSensorRepository,
    ) {
    }

    /**
     * @throws BaseReadingTypeNotFoundException
     */
    public function processTrigger(SensorTrigger $sensorTrigger): void
    {
        $sensorToTrigger = $sensorTrigger->getBaseReadingTypeToTriggers();
        if ($sensorToTrigger === null) {
            throw new BaseReadingTypeNotFoundException('Base reading type needs to be set for a relay to be activated');
        }
        $boolSensor = $this->boolReadingBaseSensorRepository->findOneBy(['baseReadingType' => $sensorToTrigger->getBaseReadingTypeID()]);
        if ($boolSensor !== null) {
            $currentReading = $boolSensor->getCurrentReading();
            $readingToBeRequested = $sensorTrigger->getTriggerType()->getTriggerTypeName() === TriggerType::RELAY_UP_TRIGGER;
            if ($currentReading !== $readingToBeRequested) {
                $updateReadingDTO = $this->updateSensorCurrentReadingDTOBuilder->buildSensorSwitchRequestConsumerMessageDTO(
                    $sensorToTrigger->getSensor()->getSensorID(),
                    BoolCurrentReadingUpdateDTOBuilder::buildCurrentReadingUpdateDTO(
                        Relay::READING_TYPE,
                        $readingToBeRequested
                    ),
                );
                $this->sendCurrentReadingAMQPProducer->publish(serialize($updateReadingDTO));
            }
        }
    }
}