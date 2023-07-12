<?php

namespace App\Sensors\SensorServices\SensorReadingUpdate\RequestReading;

use App\Devices\Exceptions\DeviceIPNotSetException;
use App\Devices\Factories\DeviceRequestArgumentBuilderFactory;
use App\Sensors\DTO\Internal\CurrentReadingDTO\AMQPDTOs\RequestSensorCurrentReadingUpdateMessageDTO;
use App\Sensors\Entity\SensorTypes\GenericRelay;
use App\Sensors\Exceptions\SensorNotFoundException;
use App\Sensors\Exceptions\SensorTypeException;
use App\Sensors\Factories\SensorType\SensorTypeRepositoryFactory;
use App\Sensors\Repository\Sensors\SensorRepositoryInterface;

readonly class RequestSensorCurrentReadingHandler implements RequestSensorCurrentReadingHandlerInterface
{
    public function __construct(
        private SensorRepositoryInterface $sensorRepository,
        private SensorTypeRepositoryFactory $sensorTypeRepositoryFactory,
        private DeviceRequestArgumentBuilderFactory $deviceRequestArgumentBuilderFactory,
    ) {}

    /**
     * @throws SensorNotFoundException
     * @throws DeviceIPNotSetException
     * @throws SensorTypeException
     */
    public function handleUpdateSensor(RequestSensorCurrentReadingUpdateMessageDTO $currentReadingUpdateMessageDTO): bool
    {
        $sensor = $this->sensorRepository->find($currentReadingUpdateMessageDTO->getSensorID());
        if ($sensor === null) {
            throw new SensorNotFoundException();
        }

        $device = $sensor->getDevice();
        if ($device->getIpAddress() === null) {
            throw new DeviceIPNotSetException();
        }


//        $currentReadingUpdateMessageDTO->getSensorRequestType()

        //get current update request argument builder

        $requestArgumentBuilder = $this->deviceRequestArgumentBuilderFactory->fetchDeviceRequestArgumentBuilder(DeviceRequestArgumentBuilderFactory::UPDATE_SENSOR_CURRENT_READING);
        // pass the dto returned from the argument builder to the http client
        $requestArgumentBuilder->buildDeviceRequestArguments();

        // on success return true and set current reading of the sensorreading type to requested value
        $sensorTypeRepository = $this->sensorTypeRepositoryFactory->getSensorTypeRepository($sensor->getSensorTypeObject()->getSensorType());

        $sensorType = $sensorTypeRepository->findOneBy(['sensor' => $sensor->getSensorID()]);

        if ($sensorType instanceof GenericRelay) {
            $sensorType->getRelay()->setCurrentReading($currentReadingUpdateMessageDTO->getReadingTypeCurrentReadingDTO()->getCurrentReading());
        } else {
            throw new SensorTypeException(sprintf(SensorTypeException::SENSOR_TYPE_NOT_ALLOWED, $sensorType->getSensorTypeName()));
        }
        return true;
    }
}
