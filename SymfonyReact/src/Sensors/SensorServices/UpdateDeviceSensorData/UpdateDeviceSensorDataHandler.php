<?php

namespace App\Sensors\SensorServices\UpdateDeviceSensorData;

use App\Common\Services\DeviceRequestHandlerInterface;
use App\Devices\Builders\Request\DeviceRequestEncapsulationBuilder;
use App\Devices\Builders\Request\DeviceSettingsRequestDTOBuilder;
use App\Sensors\Builders\SensorRequestBuilders\SensorTypeDataRequestEncapsulationDTOBuilder;
use App\Sensors\Builders\SensorUpdateRequestDTOBuilder\SingleSensorUpdateRequestDTOBuilder;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\GenericMotion;
use App\Sensors\Entity\SensorTypes\GenericRelay;
use App\Sensors\Entity\SensorTypes\Soil;
use App\Sensors\Exceptions\SensorNotFoundException;
use App\Sensors\Exceptions\SensorRequestException;
use App\Sensors\Exceptions\SensorTypeException;
use App\Sensors\Factories\SensorType\SensorTypeRepositoryFactory;
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
        private SensorTypeRepositoryFactory $sensorTypeRepositoryFactory,
        private SingleSensorUpdateRequestDTOBuilder $singleSensorUpdateRequestDTOBuilder,
        private DeviceSettingsRequestDTOBuilder $deviceSettingsRequestDTOBuilder,
        private LoggerInterface $logger,
    ) {}

    /**
     * @throws SensorRequestException
     * @throws SensorNotFoundException
     * @throws SensorTypeException
     */
    public function handleSensorsUpdateRequest(array $sensorIDs): bool
    {
        // need to order by so that bus sensors get added to the json in the correct order
        $sensors = $this->sensorRepository->findBy(['sensorID' => $sensorIDs], ['createdAt' => 'DESC']);
        if (empty($sensors)) {
            throw new SensorNotFoundException(sprintf('Error processing sensor data to upload sensor not found, sensor ids: %s', implode(', ', $sensorIDs)));
        }

        $sensorData = [];
        foreach ($sensors as $sensor) {
            try {
                $sensorTypeRepository = $this->sensorTypeRepositoryFactory->getSensorTypeRepository($sensor->getSensorTypeObject()->getSensorType());
            } catch (SensorTypeException $e) {
                $this->logger->error($e->getMessage());
                continue;
            }
            $sensorType = $sensorTypeRepository->findOneBy(['sensor' => $sensor->getSensorID()]);
            if ($sensorType === null) {
                $this->logger->error(sprintf('Error processing sensor data to upload sensor type not found, sensor id: %s, sensor type id: %s', $sensor->getSensorID(), $sensor->getSensorTypeObject()->getSensorTypeID()));

                return true;
            }
            $sensorTypeName = $sensorType->getSensorTypeName();

            $sensorData[$sensorTypeName][] = $this->singleSensorUpdateRequestDTOBuilder->buildSensorUpdateRequestDTO(
                $sensor,
            );
        }

        if (empty($sensorData)) {
            throw new SensorNotFoundException('No sensor data to send to device');
        }

        $sensorTypeDataRequestEncapsulationDTO = SensorTypeDataRequestEncapsulationDTOBuilder::buildSensorTypeDataRequestDTO(
            $sensorData[GenericRelay::NAME] ?? [],
            $sensorData[Dht::NAME] ?? [],
            $sensorData[Dallas::NAME] ?? [],
            $sensorData[Soil::NAME] ?? [],
            $sensorData[GenericMotion::NAME] ?? [],
            $sensorData[Bmp::NAME] ?? [],
        );

        $deviceSettingsRequestDTO = $this->deviceSettingsRequestDTOBuilder->buildDeviceSettingsRequestDTO(
            sensorData: $sensorTypeDataRequestEncapsulationDTO,
        );
        $groups = array_keys($sensorData);
        $groups[] = DeviceSettingsRequestDTOBuilder::SENSOR_DATA;

        $device = $sensors[0]->getDevice();

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
        } catch (Exception) {
            return false;
        }

        return false;
    }
}
