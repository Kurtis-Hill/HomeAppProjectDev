<?php

namespace App\Services\Sensor\SensorReadingUpdate\RequestReading;

use App\Builders\Device\Request\DeviceRequestEncapsulationBuilder;
use App\DTOs\Sensor\Internal\CurrentReadingDTO\AMQPDTOs\RequestSensorCurrentReadingUpdateTransportMessageDTO;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Relay;
use App\Entity\Sensor\SensorTypes\GenericRelay;
use App\Exceptions\Device\DeviceIPNotSetException;
use App\Exceptions\Device\DeviceRequestArgumentBuilderTypeNotFoundException;
use App\Exceptions\Sensor\SensorNotFoundException;
use App\Exceptions\Sensor\SensorPinNumberNotSetException;
use App\Exceptions\Sensor\SensorReadingTypeRepositoryFactoryException;
use App\Factories\Device\DeviceSensorRequestArgumentBuilderFactory;
use App\Factories\Sensor\SensorReadingType\SensorReadingTypeRepositoryFactory;
use App\Repository\Sensor\Sensors\SensorRepositoryInterface;
use App\Services\Device\Request\DeviceRequestHandlerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

readonly class SensorUpdateCurrentReadingRequestHandler implements SensorUpdateCurrentReadingRequestHandlerInterface
{
    public const SENSOR_SWITCH_ENDPOINT = 'switch';

    public function __construct(
        private SensorRepositoryInterface $sensorRepository,
        private SensorReadingTypeRepositoryFactory $sensorReadingTypeRepositoryFactory,
        private DeviceSensorRequestArgumentBuilderFactory $deviceSensorRequestArgumentBuilderFactory,
        private DeviceRequestHandlerInterface $deviceRequestHandler,
    ) {}

    /**
     * @param RequestSensorCurrentReadingUpdateTransportMessageDTO $currentReadingUpdateMessageDTO
     * @return bool
     * @throws DeviceIPNotSetException
     * @throws DeviceRequestArgumentBuilderTypeNotFoundException
     * @throws SensorNotFoundException
     * @throws SensorPinNumberNotSetException
     * @throws SensorReadingTypeRepositoryFactoryException
     * @throws ORMException
     * @throws HttpException
     * @throws ExceptionInterface
     * @throws TransportExceptionInterface|\HttpException
     */
    public function handleUpdateSensorReadingRequest(RequestSensorCurrentReadingUpdateTransportMessageDTO $currentReadingUpdateMessageDTO): bool
    {
        $sensor = $this->sensorRepository->findSensorByIDNoCache($currentReadingUpdateMessageDTO->getSensorID());
        if ($sensor === null) {
            throw new SensorNotFoundException();
        }
        $readingTypeCurrentReadingDTO = $currentReadingUpdateMessageDTO->getReadingTypeCurrentReadingDTO();

        $requestArgumentBuilder = $this->deviceSensorRequestArgumentBuilderFactory->fetchDeviceRequestArgumentBuilder(DeviceSensorRequestArgumentBuilderFactory::UPDATE_SENSOR_CURRENT_READING);

        $requestArguments = $requestArgumentBuilder->buildSensorRequestArguments($sensor, $readingTypeCurrentReadingDTO);

        $deviceEncapsulationRequestDTO = DeviceRequestEncapsulationBuilder::buildDeviceRequestEncapsulation(
            device: $sensor->getDevice(),
            deviceRequestDTO: $requestArguments,
            endpoint: self::SENSOR_SWITCH_ENDPOINT
        );

        $relay = $this->sensorReadingTypeRepositoryFactory->getSensorReadingTypeRepository(Relay::getReadingTypeName())->findOneBySensorNameID($sensor->getSensorID());
        if (!$relay instanceof Relay) {
            throw new SensorReadingTypeRepositoryFactoryException();
        }

        $deviceResponse = $this->deviceRequestHandler->handleDeviceRequest(
            $deviceEncapsulationRequestDTO
        );

        if ($deviceResponse->getStatusCode() === Response::HTTP_OK) {
            $sensorTypeObject = $sensor->getSensorTypeObject();
            if ($sensorTypeObject instanceof GenericRelay) {
                $relayRepository = $this->sensorReadingTypeRepositoryFactory->getSensorReadingTypeRepository(Relay::getReadingTypeName());
                $relay = $relayRepository->findOneBySensorNameID($sensor->getSensorID());
                if (!$relay instanceof Relay) {
                    throw new SensorReadingTypeRepositoryFactoryException();
                }
                $relay->setCurrentReading(
                    $readingTypeCurrentReadingDTO->getCurrentReading()
                );
                $relay->setUpdatedAt();
                $relayRepository->flush();
            }

            return true;
        }

        return false;
    }
}
