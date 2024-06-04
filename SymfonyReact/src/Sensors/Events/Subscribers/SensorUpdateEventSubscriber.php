<?php

namespace App\Sensors\Events\Subscribers;

use App\Sensors\Events\SensorUpdateEvent;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Service\Attribute\Required;

class SensorUpdateEventSubscriber implements EventSubscriberInterface
{
    private ProducerInterface $sensorDataUpdateProducer;

    public static function getSubscribedEvents(): array
    {
        return [
             SensorUpdateEvent::NAME => 'onSensorUpdate',
        ];
    }

    public function onSensorUpdate(SensorUpdateEvent $sensorUpdateEvent): void
    {
        $this->sensorDataUpdateProducer->publish(serialize($sensorUpdateEvent->getSensorUpdateEventDTO()));
    }

    #[Required]
    public function setSensorDataUpdateProducer(ProducerInterface $producer): void
    {
        $this->sensorDataUpdateProducer = $producer;
    }
}
