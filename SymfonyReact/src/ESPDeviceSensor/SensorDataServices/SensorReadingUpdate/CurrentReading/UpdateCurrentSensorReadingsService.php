<?php

namespace App\ESPDeviceSensor\SensorDataServices\SensorReadingUpdate\CurrentReading;

use App\Devices\Entity\Devices;
use App\ESPDeviceSensor\DTO\Sensor\UpdateSensorCurrentReadingDTO;
use App\ESPDeviceSensor\Factories\ORMFactories\ConstRecord\ORMConstRecordFactoryInterface;
use App\ESPDeviceSensor\Factories\ORMFactories\OufOfBounds\OutOfBoundsFactoryInterface;
use App\ESPDeviceSensor\Factories\SensorTypeQueryDTOFactory\SensorTypeQueryFactory;
use App\ESPDeviceSensor\Repository\ORM\Sensors\SensorRepositoryInterface;
use App\ESPDeviceSensor\SensorDataServices\SensorReadingTypesValidator\SensorReadingTypesValidatorServiceInterface;

//@TODO make rabbit mq autoload docker config
class UpdateCurrentSensorReadingsService implements UpdateCurrentSensorReadingInterface
{
    private SensorRepositoryInterface $sensorRepository;

    private SensorTypeQueryFactory $sensorTypeQueryFactory;

    private SensorReadingTypesValidatorServiceInterface $readingTypesValidator;

    private ORMConstRecordFactoryInterface $constRecordFactory;

    private OutOfBoundsFactoryInterface $outOfBoundsFactory;

    public function __construct(
        SensorRepositoryInterface $sensorRepository,
        SensorTypeQueryFactory $readingTypeQueryFactory,
        SensorReadingTypesValidatorServiceInterface $readingTypesValidator,
        ORMConstRecordFactoryInterface $constRecordFactory,
        OutOfBoundsFactoryInterface $outOfBoundsFactory,

    )
    {
        $this->sensorRepository = $sensorRepository;
        $this->sensorTypeQueryFactory = $readingTypeQueryFactory;
    }

    public function handleUpdateSensorCurrentReading(
        UpdateSensorCurrentReadingDTO $updateSensorReadingDTO,
        Devices $device
    ): bool
    {
        $sensorTypeQueryDTOBuilder = $this->sensorTypeQueryFactory->getSensorTypeQueryDTOBuilder(
            $updateSensorReadingDTO->getSensorType()
        );

        $sensorReadingTypeQueryDTOs = $sensorTypeQueryDTOBuilder->buildSensorReadingTypes();

        $sensorReadingObjects = $this->sensorRepository->getSensorTypeAndReadingTypeObjectsForSensor(
            $updateSensorReadingDTO->getDeviceId(),
            $updateSensorReadingDTO->getSensorName(),
            null,
            $sensorReadingTypeQueryDTOs,
        );

        dd($sensorReadingObjects);
//        dd($sensorReadingTypeQueryDTOs, $sensorTypeJoinQueryDTO);

//        $this->sensorRepository->getSensorReadingTypeDataBySensor(
//
//        )
        dd($sensorReadingTypeQueryDTOs);
        return false;
    }

    private function updateCurrentSensorReading
}
