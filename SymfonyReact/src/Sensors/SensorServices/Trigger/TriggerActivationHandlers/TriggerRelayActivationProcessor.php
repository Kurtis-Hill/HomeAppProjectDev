<?php

namespace App\Sensors\SensorServices\Trigger\TriggerActivationHandlers;

use App\Common\Entity\TriggerType;
use App\Sensors\Builders\CurrentReadingDTOBuilders\BoolCurrentReadingUpdateDTOBuilder;
use App\Sensors\Builders\MessageDTOBuilders\UpdateSensorCurrentReadingDTOBuilder;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Relay;
use App\Sensors\Entity\SensorTrigger;
use App\Sensors\Repository\SensorReadingType\ORM\BoolReadingBaseSensorRepository;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;

readonly class TriggerRelayActivationProcessor implements TriggerProcessorInterface
{
    public function __construct(
        private UpdateSensorCurrentReadingDTOBuilder $updateSensorCurrentReadingDTOBuilder,
        private ProducerInterface $sendCurrentReadingAMQPProducer,
        private BoolReadingBaseSensorRepository $boolReadingBaseSensorRepository,
    ) {
    }

    public function processTrigger(SensorTrigger $sensorTrigger): void
    {
        $sensorToTrigger = $sensorTrigger->getBaseReadingTypeToTriggerID();
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
