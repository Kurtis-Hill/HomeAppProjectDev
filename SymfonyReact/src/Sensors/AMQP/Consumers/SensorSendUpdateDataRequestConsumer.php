<?php

namespace App\Sensors\AMQP\Consumers;

use App\Sensors\DTO\Internal\Event\SensorUpdateEventDTO;
use App\Sensors\DTO\Request\SendRequests\SensorDataUpdate\SingleSensorUpdateRequestDTO;
use App\Sensors\Exceptions\SensorNotFoundException;
use App\Sensors\Exceptions\SensorRequestException;
use App\Sensors\SensorServices\UpdateDeviceSensorData\UpdateDeviceSensorDataHandler;
use Exception;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

readonly class SensorSendUpdateDataRequestConsumer implements ConsumerInterface
{
    public function __construct(
        private UpdateDeviceSensorDataHandler $updateDeviceSensorDataHandler,
        private LoggerInterface $logger,
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
                        SingleSensorUpdateRequestDTO::class,
                    ]
                ]
            );
        } catch (Exception $exception) {
            $this->logger->error('Deserialization of message failure, check the message has been sent to the correct queue, exception message: ' . $exception->getMessage());

            return true;
        }
        try {
            $updateRequestResult = $this->updateDeviceSensorDataHandler->handleSensorsUpdateRequest($sensorUpdateEventDTO->getSensorUpdateRequestDTOs());
            if ($updateRequestResult === false) {
                $this->logger->error('Error processing sensor data to upload sensor not found, sensor name: ' . $sensorUpdateEventDTO->getSensorUpdateRequestDTOs()[0]->getSensorName());
            } else {
                $this->logger->info('Sensor data update handled successfully, sensor name: ' . $sensorUpdateEventDTO->getSensorUpdateRequestDTOs()[0]->getSensorName());
            }
            return $updateRequestResult;
        } catch (SensorRequestException $e) {
            $this->logger->error('Sensor request exception, exception message: ' . $e->getMessage());

            return false;
        } catch (SensorNotFoundException $e) {
            $this->logger->error($e->getMessage());
        }

        return true;
    }
}
