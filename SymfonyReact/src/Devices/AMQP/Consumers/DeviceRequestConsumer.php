<?php

namespace App\Devices\AMQP\Consumers;

use App\Devices\Exceptions\DeviceIPNotSetException;
use App\Sensors\DTO\Internal\CurrentReadingDTO\AMQPDTOs\RequestSensorCurrentReadingUpdateMessageDTO;
use App\Sensors\Exceptions\SensorNotFoundException;
use App\Sensors\Exceptions\SensorTypeException;
use App\Sensors\SensorServices\SensorReadingUpdate\RequestReading\SensorUpdateCurrentReadingRequestHandlerInterface;
use Exception;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

readonly class DeviceRequestConsumer implements ConsumerInterface
{
    public function __construct(
        private LoggerInterface $elasticLogger,
        private SensorUpdateCurrentReadingRequestHandlerInterface $requestSensorCurrentReadingHandler,
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

        try {
            $requestSensorCurrentReadingHandler = $this->requestSensorCurrentReadingHandler->handleUpdateSensor($sensorData);
        } catch (SensorNotFoundException | DeviceIPNotSetException | SensorTypeException | ExceptionInterface $exception) {
            $this->elasticLogger->error('Sensor update request failed, exception message: ' . $exception->getMessage());

            return true;
        }

        return $requestSensorCurrentReadingHandler;
    }
}
