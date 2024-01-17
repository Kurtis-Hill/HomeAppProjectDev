<?php

namespace App\Sensors\SensorServices\Trigger\TriggerActivationHandlers;

use App\Common\Entity\TriggerType;
use App\Sensors\Builders\CurrentReadingDTOBuilders\BoolCurrentReadingUpdateDTOBuilder;
use App\Sensors\Builders\MessageDTOBuilders\UpdateSensorCurrentReadingDTOBuilder;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Relay;
use App\Sensors\Entity\SensorTrigger;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Symfony\Contracts\Service\Attribute\Required;

class TriggerRelayActivationHandler implements TriggerHandlerInterface
{
    private ProducerInterface $sendCurrentReadingAMQPProducer;

    public function __construct(
        private readonly UpdateSensorCurrentReadingDTOBuilder $updateSensorCurrentReadingDTOBuilder,
    ) {
    }

    public function processTrigger(SensorTrigger $sensorTrigger): void
    {
        $sensorToTrigger = $sensorTrigger->getBaseReadingTypeToTriggerID();
        $updateReadingDTO = $this->updateSensorCurrentReadingDTOBuilder->buildSensorSwitchRequestConsumerMessageDTO(
            $sensorToTrigger->getSensor()->getSensorID(),
            BoolCurrentReadingUpdateDTOBuilder::buildCurrentReadingUpdateDTO(
                Relay::READING_TYPE,
                $sensorTrigger->getTriggerType()->getTriggerTypeName() === TriggerType::RELAY_UP_TRIGGER
            ),
        );

        $this->sendCurrentReadingAMQPProducer->publish(serialize($updateReadingDTO));
    }

    #[Required]
    public function setESPSendCurrentReadingProducer(ProducerInterface $producer): void
    {
        $this->sendCurrentReadingAMQPProducer = $producer;
    }
}
