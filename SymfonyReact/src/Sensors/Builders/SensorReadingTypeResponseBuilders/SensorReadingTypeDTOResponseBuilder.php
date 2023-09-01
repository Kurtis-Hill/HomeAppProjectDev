<?php

namespace App\Sensors\Builders\SensorReadingTypeResponseBuilders;

use App\Sensors\DTO\Response\SensorReadingTypeResponse\SensorReadingTypeResponseDTOInterface;
use App\Sensors\DTO\Response\SensorReadingTypeResponse\StandardReadingTypeResponseInterface;
use App\Sensors\Entity\ReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\Temperature;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Interfaces\AnalogSensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\HumiditySensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\LatitudeSensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\StandardSensorReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\TemperatureSensorTypeInterface;
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

        if ($sensorTypeObject instanceof StandardSensorReadingTypeInterface) {
            return $this->handleStandardSensorReadingTypeDTOCreation($sensorTypeObject);
        }

        return [];
    }

    /**
     * @return SensorReadingTypeResponseDTOInterface[]
     * @throws ReadingTypeNotExpectedException
     */
    #[ArrayShape([StandardReadingTypeResponseInterface::class])]
    private function handleStandardSensorReadingTypeDTOCreation(StandardSensorReadingTypeInterface $sensorTypeObject): array
    {
        if ($sensorTypeObject instanceof TemperatureSensorTypeInterface) {
            $sensorReadingTypeResponseBuilder = $this->sensorReadingTypeResponseFactory->getSensorReadingTypeDTOResponseBuilder(Temperature::getReadingTypeName());
            $sensorReadingTypeResponseDTOs[Temperature::READING_TYPE] = $sensorReadingTypeResponseBuilder->buildSensorReadingTypeResponseDTO($sensorTypeObject->getTemperature());
        }
        if ($sensorTypeObject instanceof HumiditySensorTypeInterface) {
            $sensorReadingTypeResponseBuilder = $this->sensorReadingTypeResponseFactory->getSensorReadingTypeDTOResponseBuilder(Humidity::getReadingTypeName());
            $sensorReadingTypeResponseDTOs[Humidity::READING_TYPE] = $sensorReadingTypeResponseBuilder->buildSensorReadingTypeResponseDTO($sensorTypeObject->getHumidObject());
        }
        if ($sensorTypeObject instanceof LatitudeSensorTypeInterface) {
            $sensorReadingTypeResponseBuilder = $this->sensorReadingTypeResponseFactory->getSensorReadingTypeDTOResponseBuilder(Latitude::getReadingTypeName());
            $sensorReadingTypeResponseDTOs[Latitude::READING_TYPE] = $sensorReadingTypeResponseBuilder->buildSensorReadingTypeResponseDTO($sensorTypeObject->getLatitudeObject());
        }
        if ($sensorTypeObject instanceof AnalogSensorTypeInterface) {
            $sensorReadingTypeResponseBuilder = $this->sensorReadingTypeResponseFactory->getSensorReadingTypeDTOResponseBuilder(Analog::getReadingTypeName());
            $sensorReadingTypeResponseDTOs[Analog::READING_TYPE] = $sensorReadingTypeResponseBuilder->buildSensorReadingTypeResponseDTO($sensorTypeObject->getAnalogObject());
        }

        if (empty($sensorReadingTypeResponseDTOs)) {
            throw new ReadingTypeNotExpectedException(ReadingTypeNotSupportedException::READING_TYPE_NOT_SUPPORTED_UPDATE_APP_MESSAGE);
        }

        return $sensorReadingTypeResponseDTOs;
    }
}
