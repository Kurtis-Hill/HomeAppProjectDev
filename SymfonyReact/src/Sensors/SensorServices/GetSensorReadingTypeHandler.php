<?php

namespace App\Sensors\SensorServices;

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
use App\Sensors\Entity\SensorTypes\Interfaces\StandardSensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\TemperatureSensorTypeInterface;
use App\Sensors\Exceptions\ReadingTypeNotExpectedException;
use App\Sensors\Exceptions\ReadingTypeNotSupportedException;
use App\Sensors\Factories\SensorReadingType\SensorReadingTypeResponseFactory;
use App\Sensors\Factories\SensorType\SensorTypeRepositoryFactory;
use JetBrains\PhpStorm\ArrayShape;

class GetSensorReadingTypeHandler
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
    public function handleSensorReadingTypeDTOCreating(Sensor $sensor): array
    {
        $sensorType = $sensor->getSensorTypeObject();

        $sensorReadingTypeRepository = $this->sensorTypeRepositoryFactory->getSensorTypeRepository($sensorType->getSensorType());
        $sensorTypeObject = $sensorReadingTypeRepository->findOneBy(['sensor' => $sensor]);

        if ($sensorTypeObject instanceof StandardSensorTypeInterface) {
            return $this->handleStandardSensorTypeDTOCreation($sensorTypeObject);
        }

        return [];
    }

    /**
     * @return SensorReadingTypeResponseDTOInterface[]
     * @throws ReadingTypeNotExpectedException
     */
    #[ArrayShape([StandardReadingTypeResponseInterface::class])]
    private function handleStandardSensorTypeDTOCreation(StandardSensorTypeInterface $sensorTypeObject): array
    {
        if ($sensorTypeObject instanceof TemperatureSensorTypeInterface) {
            $sensorReadingTypeResponseBuilder = $this->sensorReadingTypeResponseFactory->getSensorReadingTypeDTOResponseBuilder(Temperature::getReadingTypeName());
            $sensorReadingTypeResponseDTOs[] = $sensorReadingTypeResponseBuilder->buildSensorReadingTypeResponseDTO($sensorTypeObject->getTemperature());
        }
        if ($sensorTypeObject instanceof HumiditySensorTypeInterface) {
            $sensorReadingTypeResponseBuilder = $this->sensorReadingTypeResponseFactory->getSensorReadingTypeDTOResponseBuilder(Humidity::getReadingTypeName());
            $sensorReadingTypeResponseDTOs[] = $sensorReadingTypeResponseBuilder->buildSensorReadingTypeResponseDTO($sensorTypeObject->getHumidObject());
        }
        if ($sensorTypeObject instanceof LatitudeSensorTypeInterface) {
            $sensorReadingTypeResponseBuilder = $this->sensorReadingTypeResponseFactory->getSensorReadingTypeDTOResponseBuilder(Latitude::getReadingTypeName());
            $sensorReadingTypeResponseDTOs[] = $sensorReadingTypeResponseBuilder->buildSensorReadingTypeResponseDTO($sensorTypeObject->getLatitudeObject());
        }
        if ($sensorTypeObject instanceof AnalogSensorTypeInterface) {
            $sensorReadingTypeResponseBuilder = $this->sensorReadingTypeResponseFactory->getSensorReadingTypeDTOResponseBuilder(Analog::getReadingTypeName());
            $sensorReadingTypeResponseDTOs[] = $sensorReadingTypeResponseBuilder->buildSensorReadingTypeResponseDTO($sensorTypeObject->getAnalogObject());
        }

        if (empty($sensorReadingTypeResponseDTOs)) {
            throw new ReadingTypeNotExpectedException(ReadingTypeNotSupportedException::READING_TYPE_NOT_SUPPORTED_UPDATE_APP_MESSAGE);
        }

        return $sensorReadingTypeResponseDTOs;
    }
}
