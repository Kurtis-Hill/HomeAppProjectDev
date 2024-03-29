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
        private DeviceSettingsUpdateRequestHandler $deviceSettingsUpdateRequestHandler,
        private LoggerInterface $elasticLogger,
    ) {}

    public function execute(AMQPMessage $msg): bool
    {
        try {
            /** @var DeviceSettingsUpdateDTO $deviceUpdateRequestDTO */
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
            $result = $this->deviceSettingsUpdateRequestHandler->handleDeviceSettingsUpdateRequest($deviceUpdateRequestDTO);
            if ($result === true) {
                $this->elasticLogger->info(sprintf('Device settings update request successful for device %s', $deviceUpdateRequestDTO->getDeviceId()));
            } else {
                $this->elasticLogger->error(sprintf('Device settings update request failed for device %s', $deviceUpdateRequestDTO->getDeviceId()));
            }

            return $result;
        } catch (DeviceNotFoundException) {
            $this->elasticLogger->error('Device settings update request failed, device not found');

            return false;
        } catch (Exception $exception) {
            $this->elasticLogger->error('Device settings update request failed with unexpected error, exception message: ' . $exception->getMessage());

            return false;
        }
    }
}
