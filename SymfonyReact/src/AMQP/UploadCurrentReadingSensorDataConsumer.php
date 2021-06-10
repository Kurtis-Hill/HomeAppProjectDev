<?php


namespace App\AMQP;


use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;

class UploadCurrentReadingSensorDataConsumer implements ConsumerInterface
{
    public function execute(AMQPMessage $msg): bool
    {
        $sensorData = unserialize($msg->body());

        $sensorDataHandled = $this->handleSensorDataRequest($sensorData);

//        if ($sensorDataHandled !== true) {
//            return false;
//        }

        echo $sensorData;

        return true;
    }

    private function handleSensorDataRequest(array $sensorData): bool
    {

    }

}
