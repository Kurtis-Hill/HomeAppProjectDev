<?php
declare(strict_types=1);

namespace App\AMQP\Device\Consumers;

use App\DTOs\Sensor\Internal\CurrentReadingDTO\AMQPDTOs\RequestSensorCurrentReadingUpdateTransportMessageDTO;
use App\DTOs\Sensor\Internal\CurrentReadingDTO\BoolCurrentReadingUpdateDTO;
use App\Exceptions\Device\DeviceIPNotSetException;
use App\Exceptions\Sensor\SensorNotFoundException;
use App\Exceptions\Sensor\SensorReadingTypeRepositoryFactoryException;
use App\Exceptions\Sensor\SensorTypeException;
use App\Services\Sensor\SensorReadingUpdate\RequestReading\SensorUpdateCurrentReadingRequestHandlerInterface;
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
            /** @var RequestSensorCurrentReadingUpdateTransportMessageDTO $sensorData */
            $sensorData = unserialize(
                $msg->getBody(),
                [
                    'allowed_classes' => [
                        RequestSensorCurrentReadingUpdateTransportMessageDTO::class,
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
        } catch (SensorNotFoundException | DeviceIPNotSetException | SensorTypeException | ExceptionInterface | SensorReadingTypeRepositoryFactoryException $exception) {
            $this->elasticLogger->error('Sensor update request failed: ' . $exception->getMessage());

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
