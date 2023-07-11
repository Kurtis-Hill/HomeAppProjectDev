<?php

namespace App\Devices\AMQP\Consumers;

use App\Sensors\DTO\Internal\CurrentReadingDTO\AMQPDTOs\UpdateSensorCurrentReadingMessageDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\AnalogCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\BoolCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\HumidityCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\LatitudeCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\TemperatureCurrentReadingUpdateRequestDTO;
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
    ) {}
    public function execute(AMQPMessage $msg): bool
    {
        try {
            $sensorData = unserialize(
                $msg->getBody(),
                [
                    'allowed_classes' => [
                        UpdateSensorCurrentReadingMessageDTO::class,
                        AnalogCurrentReadingUpdateRequestDTO::class,
                        HumidityCurrentReadingUpdateRequestDTO::class,
                        LatitudeCurrentReadingUpdateRequestDTO::class,
                        TemperatureCurrentReadingUpdateRequestDTO::class,
                        BoolCurrentReadingUpdateRequestDTO::class,
                    ]
                ]
            );
        } catch (Exception $exception) {
            $this->logger->error('Deserialization of message failure, check the message has been sent to the correct queue, exception message: ' . $exception->getMessage());

            return true;
        }
    }
}
