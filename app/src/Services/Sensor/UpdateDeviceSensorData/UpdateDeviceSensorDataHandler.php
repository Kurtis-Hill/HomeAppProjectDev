<?php

namespace App\Services\Sensor\UpdateDeviceSensorData;

use App\Builders\Device\Request\DeviceRequestEncapsulationBuilder;
use App\Builders\Device\Request\DeviceSettingsRequestDTOBuilder;
use App\Builders\Sensor\Request\SensorRequestBuilders\SensorTypeDataRequestEncapsulationDTOBuilder;
use App\DTOs\Sensor\Request\SendRequests\SensorDataUpdate\SensorUpdateRequestDTOInterface;
use App\Entity\Sensor\SensorTypes\Bmp;
use App\Entity\Sensor\SensorTypes\Dallas;
use App\Entity\Sensor\SensorTypes\Dht;
use App\Entity\Sensor\SensorTypes\GenericMotion;
use App\Entity\Sensor\SensorTypes\GenericRelay;
use App\Entity\Sensor\SensorTypes\LDR;
use App\Entity\Sensor\SensorTypes\Sht;
use App\Entity\Sensor\SensorTypes\Soil;
use App\Exceptions\Sensor\SensorNotFoundException;
use App\Exceptions\Sensor\SensorRequestException;
use App\Exceptions\Sensor\SensorTypeException;
use App\Repository\Sensor\Sensors\SensorRepositoryInterface;
use App\Services\Device\Request\DeviceRequestHandlerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;

readonly class UpdateDeviceSensorDataHandler
{
    private const SENSOR_UPDATE_SETTING_ENDPOINT = 'settings';

    public function __construct(
        private DeviceRequestHandlerInterface $deviceRequestHandler,
        private SensorRepositoryInterface $sensorRepository,
        private DeviceSettingsRequestDTOBuilder $deviceSettingsRequestDTOBuilder,
        private LoggerInterface $logger,
    ) {}

    /**
     * @param SensorUpdateRequestDTOInterface[] $sensorUpdateRequestDTOs
     * @throws SensorNotFoundException
     * @throws SensorTypeException
     *
     * @throws SensorRequestException
     */
    public function handleSensorsUpdateRequest(array $sensorUpdateRequestDTOs): bool
    {
        // need to order by so that bus sensors get added to the json in the correct order
        $sensorData = [];
        foreach ($sensorUpdateRequestDTOs as $sensorUpdateRequestDTO) {
            $sensor = $this->sensorRepository->findOneBy(['sensorName' => $sensorUpdateRequestDTO->getSensorName()]);
            if ($sensor === null) {
                throw new SensorNotFoundException(sprintf('Error processing sensor data to upload sensor not found, sensor name: %s', $sensorUpdateRequestDTO->getSensorName()));
            }

            $sensorTypeName = $sensor->getSensorTypeObject()::getSensorTypeName();

            $sensorData[$sensorTypeName][] = $sensorUpdateRequestDTO;
        }

        if (empty($sensorData)) {
            throw new SensorNotFoundException('No sensor data to send to device');
        }

        $sensorTypeDataRequestEncapsulationDTO = SensorTypeDataRequestEncapsulationDTOBuilder::buildSensorTypeDataRequestDTO(
            relay: $sensorData[GenericRelay::NAME] ?? [],
            dht: $sensorData[Dht::NAME] ?? [],
            dallas: $sensorData[Dallas::NAME] ?? [],
            soil: $sensorData[Soil::NAME] ?? [],
            motion: $sensorData[GenericMotion::NAME] ?? [],
            bmp: $sensorData[Bmp::NAME] ?? [],
            ldr: $sensorData[LDR::NAME] ?? [],
            sht: $sensorData[Sht::NAME] ?? [],
        );

        $deviceSettingsRequestDTO = $this->deviceSettingsRequestDTOBuilder->buildDeviceSettingsRequestDTO(
            sensorData: $sensorTypeDataRequestEncapsulationDTO,
        );
        $groups = array_keys($sensorData);
        $groups[] = DeviceSettingsRequestDTOBuilder::SENSOR_DATA;

        $device = $sensor->getDevice();

        $deviceEncapsulationDTO = DeviceRequestEncapsulationBuilder::buildDeviceRequestEncapsulation(
            $device,
            $deviceSettingsRequestDTO,
            self::SENSOR_UPDATE_SETTING_ENDPOINT
        );

        try {
            $deviceResponse = $this->deviceRequestHandler->handleDeviceRequest(
                $deviceEncapsulationDTO,
                $groups,
            );
            if ($deviceResponse->getStatusCode() === Response::HTTP_OK) {
                return true;
            }
        } catch (Exception $e) {
            $this->logger->error(sprintf('Error processing sensor data to upload exception: %s', $e->getMessage()));
            return false;
        }

        return false;
    }
}
