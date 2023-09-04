<?php

namespace App\Sensors\SensorServices\SensorReadingUpdate\RequestReading;

use App\Common\Services\DeviceRequestHandlerInterface;
use App\Devices\Builders\Request\DeviceRequestEncapsulationBuilder;
use App\Devices\Exceptions\DeviceIPNotSetException;
use App\Devices\Exceptions\DeviceRequestArgumentBuilderTypeNotFoundException;
use App\Devices\Factories\DeviceSensorRequestArgumentBuilderFactory;
use App\Sensors\DTO\Internal\CurrentReadingDTO\AMQPDTOs\RequestSensorCurrentReadingUpdateMessageDTO;
use App\Sensors\Entity\SensorTypes\GenericRelay;
use App\Sensors\Exceptions\SensorNotFoundException;
use App\Sensors\Exceptions\SensorPinNumberNotSetException;
use App\Sensors\Exceptions\SensorTypeException;
use App\Sensors\Factories\SensorType\SensorTypeRepositoryFactory;
use App\Sensors\Repository\Sensors\SensorRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class SensorUpdateCurrentReadingRequestHandler implements SensorUpdateCurrentReadingRequestHandlerInterface
{
    public const SENSOR_SWITCH_ENDPOINT = 'switch';

    public function __construct(
        private SensorRepositoryInterface $sensorRepository,
        private SensorTypeRepositoryFactory $sensorTypeRepositoryFactory,
        private DeviceSensorRequestArgumentBuilderFactory $deviceSensorRequestArgumentBuilderFactory,
        private DeviceRequestHandlerInterface $deviceRequestHandler,
    ) {}

    /**
     * @throws SensorNotFoundException
     * @throws DeviceIPNotSetException
     * @throws SensorTypeException
     * @throws ExceptionInterface
     * @throws DeviceRequestArgumentBuilderTypeNotFoundException
     * @throws SensorPinNumberNotSetException
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

        // on success return true and set current reading of the sensor reading type to requested value
        $sensorTypeRepository = $this->sensorTypeRepositoryFactory->getSensorTypeRepository($sensor->getSensorTypeObject()->getSensorType());
        $sensorType = $sensorTypeRepository->findOneBy(['sensor' => $sensor->getSensorID()]);
        if (!$sensorType instanceof GenericRelay) {
            throw new SensorTypeException(sprintf(SensorTypeException::SENSOR_TYPE_NOT_ALLOWED, $sensorType->getSensorTypeName()));
        }

        $deviceResponse = $this->deviceRequestHandler->handleDeviceRequest(
            $deviceEncapsulationRequestDTO
        );
//dd($sensor);

        if ($deviceResponse->getStatusCode() === Response::HTTP_OK) {
            if ($sensorType instanceof GenericRelay) {
                $relay = $sensorType->getRelay();
                $relay->setCurrentReading(
                    $readingTypeCurrentReadingDTO->getCurrentReading()
                );
                $relay->setUpdatedAt();
            }

            $sensorTypeRepository->flush();

            return true;
        }

        return false;
    }
}
