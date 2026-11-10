<?php

declare(strict_types=1);

namespace App\Events\Subscribers\Sensor;

use App\Events\Sensor\SensorDeleteEvent;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Service\Attribute\Required;

class SensorDeletionEventSubscriber implements EventSubscriberInterface
{
    private ProducerInterface $sensorDataUpdateProducer;

    public static function getSubscribedEvents(): array
    {
        return [
            SensorDeleteEvent::NAME => 'onSensorDeletion',
        ];
    }

    public function onSensorDeletion(SensorDeleteEvent $event): void
    {
        $this->sensorDataUpdateProducer->publish(serialize($event->getSensorDeletionEventDTO()));
    }

    #[Required]
    public function setSensorDataDeletionProducer(ProducerInterface $producer): void
    {
        $this->sensorDataUpdateProducer = $producer;
    }
}
