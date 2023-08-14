<?php

namespace App\Sensors\SensorServices\UpdateDeviceSensorData;

use App\Common\Services\DeviceRequestHandlerInterface;
use App\Devices\Builders\Request\DeviceRequestEncapsulationBuilder;
use App\Devices\DTO\Request\DeviceRequest\DeviceRequestDTOInterface;
use App\Sensors\DTO\Request\SendRequests\SensorDataUpdate\SensorUpdateEncapsulationInterface;
use App\Sensors\DTO\Request\SendRequests\SensorDataUpdate\SensorUpdateRequestDTOInterface;
use App\Sensors\Entity\Sensor;
use App\Sensors\Exceptions\SensorRequestException;
use App\Sensors\Exceptions\SensorTypeNotFoundException;
use App\Sensors\Factories\SensorUpdateRequestFactory\SensorUpdateRequestBuilderFactory;
use Exception;
use Symfony\Component\HttpFoundation\Response;

readonly class UpdateDeviceSensorDataHandler
{
    private const SENSOR_UPDATE_SETTING_ENDPOINT = 'settings';

    public function __construct(
        private SensorUpdateRequestBuilderFactory $sensorUpdateRequestBuilderFactory,
        private DeviceRequestHandlerInterface $deviceRequestHandler,
    ) {}

    /**
     * @throws SensorTypeNotFoundException
     */
    public function prepareSensorDataRequestDTO(Sensor $sensor): SensorUpdateRequestDTOInterface
    {
        $sensorUpdateRequestBuilder = $this->sensorUpdateRequestBuilderFactory->getSensorUpdateRequestBuilder($sensor->getSensorTypeObject()->getSensorType());

        return $sensorUpdateRequestBuilder->buildSensorUpdateRequestDTO(
            $sensor,
        );
    }

    /**
     * @throws SensorRequestException
     */
    public function sendSensorDataRequestToDevice(Sensor $sensor, SensorUpdateEncapsulationInterface $sensorUpdateRequestDTO): bool
    {
        if (!$sensorUpdateRequestDTO instanceof DeviceRequestDTOInterface) {
            throw new SensorRequestException(['DTO is not ready to be sent to device, check the DTO is an instance of DeviceRequestDTOInterface']);
        }

        $deviceEncapsulationDTO = DeviceRequestEncapsulationBuilder::buildDeviceRequestEncapsulation(
            $sensor->getDevice(),
            $sensorUpdateRequestDTO,
            self::SENSOR_UPDATE_SETTING_ENDPOINT
        );

        try {
            $deviceResponse = $this->deviceRequestHandler->handleDeviceRequest(
                $deviceEncapsulationDTO,

            );
            if ($deviceResponse->getStatusCode() === Response::HTTP_OK) {
                return true;
            }
        } catch (Exception) {
            return false;
        }

        return false;
    }
}
