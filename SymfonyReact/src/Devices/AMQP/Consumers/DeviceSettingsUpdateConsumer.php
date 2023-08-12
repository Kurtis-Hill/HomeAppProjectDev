<?php

namespace App\Devices\AMQP\Consumers;

use App\Devices\DeviceServices\Request\DeviceSettingsUpdateRequestHandler;
use App\Devices\DTO\Internal\DeviceSettingsUpdateDTO;
use App\Sensors\Exceptions\DeviceNotFoundException;
use Exception;
use OldSound\RabbitMqBundle\RabbitMq\ConsumerInterface;
use PhpAmqpLib\Message\AMQPMessage;
use Psr\Log\LoggerInterface;

readonly class DeviceSettingsUpdateConsumer implements ConsumerInterface
{
    public function __construct(
        private LoggerInterface $elasticLogger,
        private DeviceSettingsUpdateRequestHandler $deviceSettingsUpdateRequestHandler,
    ) {}

    public function execute(AMQPMessage $msg): bool
    {
        try {
            throw new Exception('lol');
            /** @var \App\Devices\DTO\Internal\DeviceSettingsUpdateDTO $deviceUpdateRequestDTO */
            $deviceUpdateRequestDTO = unserialize(
                $msg->getBody(),
                [
                    'allowed_classes' => [
                        DeviceSettingsUpdateDTO::class,
                    ]
                ]
            );
        } catch (Exception $exception) {
            $this->elasticLogger->error('Deserialization of message failure, check the message has been sent to the correct queue, exception message: ' . $exception->getMessage());

            return true;
        }

        try {
            return $this->deviceSettingsUpdateRequestHandler->handleDeviceSettingsUpdateRequest($deviceUpdateRequestDTO);
        } catch (DeviceNotFoundException) {
            $this->elasticLogger->error('Device settings update request failed, device not found');

            return true;
        } catch (Exception $exception) {
            $this->elasticLogger->error('Device settings update request failed with unexpected error, exception message: ' . $exception->getMessage());

            return false;
        }
    }
}
