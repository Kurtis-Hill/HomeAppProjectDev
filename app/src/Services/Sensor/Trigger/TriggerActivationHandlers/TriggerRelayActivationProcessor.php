<?php

namespace App\Services\Sensor\Trigger\TriggerActivationHandlers;

use App\Builders\Sensor\Internal\AMPQMessages\CurrentReadingDTOBuilders\BoolCurrentReadingUpdateDTOBuilder;
use App\Builders\Sensor\Internal\AMPQMessages\CurrentReadingDTOBuilders\UpdateSensorCurrentReadingTransportDTOBuilder;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Relay;
use App\Entity\Sensor\SensorTrigger;
use App\Entity\Sensor\TriggerType;
use App\Exceptions\Sensor\BaseReadingTypeNotFoundException;
use App\Repository\Sensor\SensorReadingType\ORM\BoolReadingBaseSensorRepository;
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
     * @throws \App\Exceptions\Sensor\BaseReadingTypeNotFoundException
     */
    public function processTrigger(SensorTrigger $sensorTrigger): void
    {
        $sensorToTrigger = $sensorTrigger->getBaseReadingTypeToTriggers();
        if ($sensorToTrigger === null) {
            throw new BaseReadingTypeNotFoundException('Base reading type needs to be set for a relay to be activated');
        }
        $boolSensor = $this->boolReadingBaseSensorRepository->findOneBy(['baseReadingType' => $sensorToTrigger->getBaseReadingTypeID()]);
        if ($boolSensor !== null) {
//            dd($boolSensor);
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
