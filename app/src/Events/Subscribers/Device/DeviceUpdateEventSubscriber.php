<?php
declare(strict_types=1);

namespace App\Events\Subscribers\Device;

use App\Events\Device\DeviceUpdateEvent;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Contracts\Service\Attribute\Required;

class DeviceUpdateEventSubscriber implements EventSubscriberInterface
{
    private ProducerInterface $deviceUpdateProducer;

    public static function getSubscribedEvents(): array
    {
        return [
             DeviceUpdateEvent::NAME => 'onDeviceUpdate',
        ];
    }

    public function onDeviceUpdate(DeviceUpdateEvent $deviceUpdateEvent): void
    {
        $this->deviceUpdateProducer->publish(serialize($deviceUpdateEvent->getDeviceUpdateEventDTO()));
    }

    #[Required]
    public function setDeviceUpdateProducer(ProducerInterface $producer): void
    {
        $this->deviceUpdateProducer = $producer;
    }
}
