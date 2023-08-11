<?php

namespace App\Sensors\AMQP\Consumers;

use App\Sensors\DTO\Internal\Event\SensorUpdateEventDTO;
use App\Sensors\Exceptions\SensorTypeNotFoundException;
use App\Sensors\SensorServices\UpdateDeviceSensorData\UpdateDeviceSensorDataHandler;
use Exception;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

readonly class UploadSensorDataToDeviceConsumer implements ConsumerInterface
{
    public function __construct(
        private LoggerInterface $logger,
        private UpdateDeviceSensorDataHandler $updateDeviceSensorDataHandler,
    ) {}

    public function execute(AMQPMessage $msg): bool
    {
        try {
            /** @var SensorUpdateEventDTO $sensorUpdateEventDTO */
            $sensorUpdateEventDTO = unserialize(
                $msg->getBody(),
                [
                    'allowed_classes' => [
                        SensorUpdateEventDTO::class,
                    ]
                ]
            );
        } catch (Exception $exception) {
            $this->logger->error('Deserialization of message failure, check the message has been sent to the correct queue, exception message: ' . $exception->getMessage());

            return true;
        }

        try {
            $sensorDataRequestDTO = $this->updateDeviceSensorDataHandler->prepareSensorDataRequestDTO($sensorUpdateEventDTO->getSensor());
        } catch (SensorTypeNotFoundException $e) {
            $this->logger->error('Sensor type not found, exception message: ' . $e->getMessage());

            return true;
        }

        $sendDataRequestResult = $this->updateDeviceSensorDataHandler->sendSensorDataRequestToDevice(
            $sensorUpdateEventDTO->getSensor(),
            $sensorDataRequestDTO
        );

    }
}
