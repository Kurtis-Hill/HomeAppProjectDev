<?php

namespace App\Sensors\Builders\SensorReadingTypeResponseBuilders\Standard;

use App\Sensors\DTO\Response\SensorReadingTypeResponse\SensorReadingTypeResponseDTOInterface;
use App\Sensors\DTO\Response\SensorReadingTypeResponse\Standard\StandardReadingTypeResponseInterface;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Motion;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Relay;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Temperature;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\AnalogReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\HumidityReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\LatitudeReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\MotionSensorReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\RelayReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\TemperatureReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\StandardSensorTypeInterface;
use App\Sensors\Exceptions\ReadingTypeNotExpectedException;
use App\Sensors\Exceptions\ReadingTypeNotSupportedException;
use App\Sensors\Factories\SensorReadingType\SensorReadingTypeResponseFactory;
use App\Sensors\Factories\SensorType\SensorTypeRepositoryFactory;
use JetBrains\PhpStorm\ArrayShape;

class SensorReadingTypeDTOResponseBuilder
{
    private SensorReadingTypeResponseFactory $sensorReadingTypeResponseFactory;

    private SensorTypeRepositoryFactory $sensorTypeRepositoryFactory;

    public function __construct(
        SensorReadingTypeResponseFactory $sensorReadingTypeResponseFactory,
        SensorTypeRepositoryFactory $sensorTypeRepositoryFactory,
    ) {
        $this->sensorReadingTypeResponseFactory = $sensorReadingTypeResponseFactory;
        $this->sensorTypeRepositoryFactory = $sensorTypeRepositoryFactory;
    }

    /**
     * @return SensorReadingTypeResponseDTOInterface[]
     * @throws ReadingTypeNotExpectedException
     */
    #[ArrayShape([SensorReadingTypeResponseDTOInterface::class])]
    public function buildSensorReadingTypeResponseDTOs(Sensor $sensor): array
    {
        $sensorType = $sensor->getSensorTypeObject();

        $sensorReadingTypeRepository = $this->sensorTypeRepositoryFactory->getSensorTypeRepository($sensorType->getSensorType());
        $sensorTypeObject = $sensorReadingTypeRepository->findOneBy(['sensor' => $sensor]);
        if ($sensorTypeObject !== null) {
            return $this->handleSensorReadingTypeDTOCreation($sensorTypeObject);
        }
//        if ($sensorTypeObject instanceof StandardSensorTypeInterface) {
//        }

        return [];
    }

    /**
     * @return SensorReadingTypeResponseDTOInterface[]
     * @throws ReadingTypeNotExpectedException
     */
    #[ArrayShape([StandardReadingTypeResponseInterface::class])]
    private function handleSensorReadingTypeDTOCreation(SensorTypeInterface $sensorTypeObject): array
    {
        if ($sensorTypeObject instanceof TemperatureReadingTypeInterface) {
            $sensorReadingTypeResponseBuilder = $this->sensorReadingTypeResponseFactory->getSensorReadingTypeDTOResponseBuilder(Temperature::getReadingTypeName());
            $sensorReadingTypeResponseDTOs[Temperature::READING_TYPE] = $sensorReadingTypeResponseBuilder->buildSensorReadingTypeResponseDTO($sensorTypeObject->getTemperature());
        }
        if ($sensorTypeObject instanceof HumidityReadingTypeInterface) {
            $sensorReadingTypeResponseBuilder = $this->sensorReadingTypeResponseFactory->getSensorReadingTypeDTOResponseBuilder(Humidity::getReadingTypeName());
            $sensorReadingTypeResponseDTOs[Humidity::READING_TYPE] = $sensorReadingTypeResponseBuilder->buildSensorReadingTypeResponseDTO($sensorTypeObject->getHumidObject());
        }
        if ($sensorTypeObject instanceof LatitudeReadingTypeInterface) {
            $sensorReadingTypeResponseBuilder = $this->sensorReadingTypeResponseFactory->getSensorReadingTypeDTOResponseBuilder(Latitude::getReadingTypeName());
            $sensorReadingTypeResponseDTOs[Latitude::READING_TYPE] = $sensorReadingTypeResponseBuilder->buildSensorReadingTypeResponseDTO($sensorTypeObject->getLatitudeObject());
        }
        if ($sensorTypeObject instanceof AnalogReadingTypeInterface) {
            $sensorReadingTypeResponseBuilder = $this->sensorReadingTypeResponseFactory->getSensorReadingTypeDTOResponseBuilder(Analog::getReadingTypeName());
            $sensorReadingTypeResponseDTOs[Analog::READING_TYPE] = $sensorReadingTypeResponseBuilder->buildSensorReadingTypeResponseDTO($sensorTypeObject->getAnalogObject());
        }
        if ($sensorTypeObject instanceof MotionSensorReadingTypeInterface) {
            $sensorReadingTypeResponseBuilder = $this->sensorReadingTypeResponseFactory->getSensorReadingTypeDTOResponseBuilder(Motion::getReadingTypeName());
            $sensorReadingTypeResponseDTOs[Motion::READING_TYPE] = $sensorReadingTypeResponseBuilder->buildSensorReadingTypeResponseDTO($sensorTypeObject->getMotion());
        }
        if ($sensorTypeObject instanceof RelayReadingTypeInterface) {
            $sensorReadingTypeResponseBuilder = $this->sensorReadingTypeResponseFactory->getSensorReadingTypeDTOResponseBuilder(Relay::getReadingTypeName());
            $sensorReadingTypeResponseDTOs[Relay::READING_TYPE] = $sensorReadingTypeResponseBuilder->buildSensorReadingTypeResponseDTO($sensorTypeObject->getRelay());
        }

        if (empty($sensorReadingTypeResponseDTOs)) {
            throw new ReadingTypeNotExpectedException(ReadingTypeNotSupportedException::READING_TYPE_NOT_SUPPORTED_UPDATE_APP_MESSAGE);
        }

        return $sensorReadingTypeResponseDTOs;
    }
}
