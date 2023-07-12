<?php

namespace App\Devices\AMQP\Consumers;

use App\Sensors\DTO\Internal\CurrentReadingDTO\AMQPDTOs\RequestSensorCurrentReadingUpdateMessageDTO;
use App\Sensors\SensorServices\SensorReadingUpdate\RequestReading\RequestSensorCurrentReadingHandlerInterface;
use App\User\Repository\ORM\UserRepositoryInterface;
use Exception;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

class DeviceRequestConsumer implements ConsumerInterface
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private LoggerInterface $elasticLogger,
        RequestSensorCurrentReadingHandlerInterface $requestSensorCurrentReadingHandler,
    ) {}

    public function execute(AMQPMessage $msg): bool
    {
        try {
            /** @var RequestSensorCurrentReadingUpdateMessageDTO $sensorData */
            $sensorData = unserialize(
                $msg->getBody(),
                [
                    'allowed_classes' => [
                        RequestSensorCurrentReadingUpdateMessageDTO::class,
                    ]
                ]
            );


        } catch (Exception $exception) {
            $this->elasticLogger->error('Deserialization of message failure, check the message has been sent to the correct queue, exception message: ' . $exception->getMessage());

            return true;
        }
    }
}
