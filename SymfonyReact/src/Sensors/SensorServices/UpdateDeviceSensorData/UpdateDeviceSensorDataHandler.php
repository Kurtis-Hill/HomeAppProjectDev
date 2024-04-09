<?php

namespace App\Sensors\SensorServices\UpdateDeviceSensorData;

use App\Common\Services\DeviceRequestHandlerInterface;
use App\Devices\Builders\Request\DeviceRequestEncapsulationBuilder;
use App\Devices\Builders\Request\DeviceSettingsRequestDTOBuilder;
use App\Sensors\Builders\Request\SensorRequestBuilders\SensorTypeDataRequestEncapsulationDTOBuilder;
use App\Sensors\DTO\Request\SendRequests\SensorDataUpdate\SensorUpdateRequestDTOInterface;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\GenericMotion;
use App\Sensors\Entity\SensorTypes\GenericRelay;
use App\Sensors\Entity\SensorTypes\LDR;
use App\Sensors\Entity\SensorTypes\Sht;
use App\Sensors\Entity\SensorTypes\Soil;
use App\Sensors\Exceptions\SensorNotFoundException;
use App\Sensors\Exceptions\SensorRequestException;
use App\Sensors\Exceptions\SensorTypeException;
use App\Sensors\Repository\Sensors\SensorRepositoryInterface;
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
     * @throws SensorRequestException
     * @throws SensorNotFoundException
     * @throws SensorTypeException
     *
     * @param SensorUpdateRequestDTOInterface[] $sensorUpdateRequestDTOs
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

            $sensorTypeName = $sensor->getSensorTypeObject()::getReadingTypeName();

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
