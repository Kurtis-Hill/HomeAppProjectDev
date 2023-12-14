<?php

namespace App\Sensors\SensorServices\SensorReadingUpdate\RequestReading;

use App\Common\Services\DeviceRequestHandlerInterface;
use App\Devices\Builders\Request\DeviceRequestEncapsulationBuilder;
use App\Devices\Exceptions\DeviceIPNotSetException;
use App\Devices\Exceptions\DeviceRequestArgumentBuilderTypeNotFoundException;
use App\Devices\Factories\DeviceSensorRequestArgumentBuilderFactory;
use App\Sensors\DTO\Internal\CurrentReadingDTO\AMQPDTOs\RequestSensorCurrentReadingUpdateMessageDTO;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Relay;
use App\Sensors\Entity\SensorTypes\GenericRelay;
use App\Sensors\Exceptions\SensorNotFoundException;
use App\Sensors\Exceptions\SensorPinNumberNotSetException;
use App\Sensors\Exceptions\SensorReadingTypeRepositoryFactoryException;
use App\Sensors\Factories\SensorReadingType\SensorReadingTypeRepositoryFactory;
use App\Sensors\Repository\Sensors\SensorRepositoryInterface;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Symfony\Component\HttpFoundation\Response;
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
     * @param RequestSensorCurrentReadingUpdateMessageDTO $currentReadingUpdateMessageDTO
     * @return bool
     * @throws DeviceIPNotSetException
     * @throws DeviceRequestArgumentBuilderTypeNotFoundException
     * @throws SensorNotFoundException
     * @throws SensorPinNumberNotSetException
     * @throws SensorReadingTypeRepositoryFactoryException
     * @throws ORMException
     * @throws NonUniqueResultException
     * @throws OptimisticLockException
     * @throws TransportExceptionInterface
     */
    public function handleUpdateSensorReadingRequest(RequestSensorCurrentReadingUpdateMessageDTO $currentReadingUpdateMessageDTO): bool
    {
        $sensors = $this->sensorRepository->findSensorsByIDNoCache([$currentReadingUpdateMessageDTO->getSensorID()]);
        if (empty($sensors)) {
            throw new SensorNotFoundException();
        }
        $sensor = $sensors[0];
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
