<?php
declare(strict_types=1);

namespace App\AMQP\Sensor\Consumers;

use App\DTOs\Sensor\Internal\Event\SensorUpdateEventDTO;
use App\DTOs\Sensor\Request\SendRequests\SensorDataUpdate\SingleSensorUpdateRequestDTO;
use App\Exceptions\Sensor\SensorNotFoundException;
use App\Exceptions\Sensor\SensorRequestException;
use App\Services\Sensor\UpdateDeviceSensorData\UpdateDeviceSensorDataHandler;
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

    public function execute(AMQPMessage $msg): int
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

            return self::MSG_ACK;
        }
        try {
            $updateRequestResult = $this->updateDeviceSensorDataHandler->handleSensorsUpdateRequest($sensorUpdateEventDTO->getSensorUpdateRequestDTOs());
            if ($updateRequestResult === false) {
                $this->logger->error('Error processing sensor data to upload sensor not found, sensor name: ' . $sensorUpdateEventDTO->getSensorUpdateRequestDTOs()[0]->getSensorName());
            } else {
                $this->logger->info('Sensor data update handled successfully, sensor name: ' . $sensorUpdateEventDTO->getSensorUpdateRequestDTOs()[0]->getSensorName());
            }

            return $updateRequestResult === true
                ? self::MSG_ACK
                : self::MSG_REJECT;
        } catch (SensorRequestException $e) {
            $this->logger->error('Sensor request exception, exception message: ' . $e->getMessage());

            return self::MSG_REJECT;
        } catch (SensorNotFoundException $e) {
            $this->logger->error($e->getMessage());
        }

        return self::MSG_ACK;
    }
}
