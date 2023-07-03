<?php

namespace App\Devices\Consumers;

use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class DeviceRequestConsumer implements ConsumerInterface
{
    public function execute(AMQPMessage $msg): bool
    {
        //
    }
}
