<?php

namespace App\Sensors\SensorServices\SensorReadingUpdate\RequestReading;

use App\Common\API\Traits\HomeAppAPITrait;
use App\Devices\Builders\Request\DeviceRequestEncapsulationBuilder;
use App\Devices\Exceptions\DeviceIPNotSetException;
use App\Devices\Exceptions\DeviceRequestArgumentBuilderTypeNotFoundException;
use App\Devices\Factories\DeviceSensorRequestArgumentBuilderFactory;
use App\Sensors\DTO\Internal\CurrentReadingDTO\AMQPDTOs\RequestSensorCurrentReadingUpdateMessageDTO;
use App\Sensors\Entity\SensorTypes\GenericRelay;
use App\Sensors\Exceptions\SensorNotFoundException;
use App\Sensors\Exceptions\SensorTypeException;
use App\Sensors\Factories\SensorType\SensorTypeRepositoryFactory;
use App\Sensors\Repository\Sensors\SensorRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

readonly class SensorUpdateCurrentReadingRequestHandler implements SensorUpdateCurrentReadingRequestHandlerInterface
{
    use HomeAppAPITrait;

    public const SENSOR_SWITCH_ENDPOINT = 'switch';

    public function __construct(
        private SensorRepositoryInterface $sensorRepository,
        private SensorTypeRepositoryFactory $sensorTypeRepositoryFactory,
        private DeviceSensorRequestArgumentBuilderFactory $deviceSensorRequestArgumentBuilderFactory,
        private HttpClientInterface $httpClient,
    ) {}

    /**
     * @throws SensorNotFoundException
     * @throws DeviceIPNotSetException
     * @throws SensorTypeException
     * @throws ExceptionInterface
     * @throws DeviceRequestArgumentBuilderTypeNotFoundException
     */
    public function handleUpdateSensorReadingRequest(RequestSensorCurrentReadingUpdateMessageDTO $currentReadingUpdateMessageDTO): bool
    {
        $sensor = $this->sensorRepository->find($currentReadingUpdateMessageDTO->getSensorID());
        if ($sensor === null) {
            throw new SensorNotFoundException();
        }

        $device = $sensor->getDevice();
        $deviceLocalIP = $device->getIpAddress();
        if ($deviceLocalIP === null) {
            throw new DeviceIPNotSetException('Device IP address is not set');
        }

        $readingTypeCurrentReadingDTO = $currentReadingUpdateMessageDTO->getReadingTypeCurrentReadingDTO();

        $requestArgumentBuilder = $this->deviceSensorRequestArgumentBuilderFactory->fetchDeviceRequestArgumentBuilder(DeviceSensorRequestArgumentBuilderFactory::UPDATE_SENSOR_CURRENT_READING);
        $requestArguments = $requestArgumentBuilder->buildSensorRequestArguments($sensor, $readingTypeCurrentReadingDTO);

        $deviceEncapsulationRequestDTO = DeviceRequestEncapsulationBuilder::buildDeviceRequestEncapsulation(
            ipAddress: $deviceLocalIP,
            deviceRequestDTO: $requestArguments,
            endpoint: self::SENSOR_SWITCH_ENDPOINT
        );

        // on success return true and set current reading of the sensor reading type to requested value
        $sensorTypeRepository = $this->sensorTypeRepositoryFactory->getSensorTypeRepository($sensor->getSensorTypeObject()->getSensorType());

        $sensorType = $sensorTypeRepository->findOneBy(['sensor' => $sensor->getSensorID()]);
        if (!$sensorType instanceof GenericRelay) {
            throw new SensorTypeException(sprintf(SensorTypeException::SENSOR_TYPE_NOT_ALLOWED, $sensorType->getSensorTypeName()));
        }

        $normalizedResponse = $this->normalizeResponse(
            $deviceEncapsulationRequestDTO->getDeviceRequestDTO()
        );

        $deviceResponse = $this->httpClient->request(
            Request::METHOD_POST,
            $deviceEncapsulationRequestDTO->getFullSensorUrl(),
            [
                'headers' =>
                    [
                        'Content-Type' => 'application/json',
                        'Accept' => 'application/json',
                    ],
                'json' => $normalizedResponse,

            ]
        );

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
