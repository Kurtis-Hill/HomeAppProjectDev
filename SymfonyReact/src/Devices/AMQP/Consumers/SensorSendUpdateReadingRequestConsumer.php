<?php

namespace App\Devices\AMQP\Consumers;

use App\Devices\Exceptions\DeviceIPNotSetException;
use App\Sensors\DTO\Internal\CurrentReadingDTO\AMQPDTOs\RequestSensorCurrentReadingUpdateMessageDTO;
use App\Sensors\DTO\Internal\CurrentReadingDTO\BoolCurrentReadingUpdateDTO;
use App\Sensors\Exceptions\SensorNotFoundException;
use App\Sensors\Exceptions\SensorPinNumberNotSetException;
use App\Sensors\Exceptions\SensorTypeException;
use App\Sensors\SensorServices\SensorReadingUpdate\RequestReading\SensorUpdateCurrentReadingRequestHandlerInterface;
use Exception;
use HttpException;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

readonly class SensorSendUpdateReadingRequestConsumer implements ConsumerInterface
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
                        BoolCurrentReadingUpdateDTO::class,
                    ]
                ]
            );
        } catch (Exception $exception) {
            $this->elasticLogger->error('Deserialization of message failure, check the message has been sent to the correct queue, exception message: ' . $exception->getMessage());

            return true;
        }

        try {
            $result = $this->requestSensorCurrentReadingHandler->handleUpdateSensorReadingRequest($sensorData);
            if ($result) {
                $this->elasticLogger->info(sprintf('Sensor update request succeeded for sensor: %d', $sensorData->getSensorId()));
            } else {
                $this->elasticLogger->error(sprintf('Sensor update request failed for sensor: %d', $sensorData->getSensorId()));
            }
            return $result;
        } catch (SensorNotFoundException | DeviceIPNotSetException | SensorTypeException | ExceptionInterface | SensorPinNumberNotSetException $exception) {
            $this->elasticLogger->error('Sensor update request failed: ' . $exception->getMessage());
//            dd('lol', 1);

            return true;
        } catch (HttpException $exception) {
            $this->elasticLogger->error('Sensor update request failed with http exception, exception message: ' . $exception->getMessage());

            return true;
        } catch (Exception $exception) {
            $this->elasticLogger->error('Sensor update request failed with unexpected error, exception message: ' . $exception->getMessage());

            return true;
        }
    }
}
