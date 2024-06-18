<?php

namespace App\Builders\Sensor\Internal\ReadingType\ReadingTypeCreationBuilders\SensorReadingTypeCreationBuilders;

use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Motion;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Relay;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Analog;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Humidity;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Latitude;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Temperature;
use App\Entity\Sensor\Sensor;
use App\Entity\Sensor\SensorTypes\Interfaces\AnalogReadingTypeInterface;
use App\Entity\Sensor\SensorTypes\Interfaces\HumidityReadingTypeInterface;
use App\Entity\Sensor\SensorTypes\Interfaces\LatitudeReadingTypeInterface;
use App\Entity\Sensor\SensorTypes\Interfaces\MotionSensorReadingTypeInterface;
use App\Entity\Sensor\SensorTypes\Interfaces\RelayReadingTypeInterface;
use App\Entity\Sensor\SensorTypes\Interfaces\TemperatureReadingTypeInterface;
use App\Exceptions\Sensor\SensorReadingTypeRepositoryFactoryException;
use App\Exceptions\Sensor\SensorTypeException;
use App\Factories\Sensor\SensorReadingType\SensorReadingTypeCreationFactory;
use Doctrine\ORM\Exception\ORMException;
use JetBrains\PhpStorm\ArrayShape;

readonly abstract class AbstractNewReadingTypeBuilder
{
    public function __construct(
        private SensorReadingTypeCreationFactory $sensorReadingTypeCreationFactory,
    ) {}

    /**
     * @throws \App\Exceptions\Sensor\SensorReadingTypeRepositoryFactoryException
     * @throws SensorTypeException
     * @throws ORMException
     */
    #[ArrayShape([Temperature::class|Humidity::class|Latitude::class|Analog::class|Relay::class|Motion::class])]
    public function buildNewSensorTypeObjects(Sensor $sensor): array
    {
        return $this->buildSensorReadingTypeObjects($sensor);
    }

    /**
     * @throws \App\Exceptions\Sensor\SensorTypeException
     * @throws \App\Exceptions\Sensor\SensorReadingTypeRepositoryFactoryException
     * @throws ORMException
     */
    protected function buildSensorReadingTypeObjects(Sensor $sensor): array
    {
        $sensorType = $sensor->getSensorTypeObject();
        if ($sensorType instanceof TemperatureReadingTypeInterface) {
            $readingType[] = $this->sensorReadingTypeCreationFactory->getReadingTypeObjectBuilder(Temperature::getReadingTypeName())->buildReadingTypeObject($sensor);
        }

        if ($sensorType instanceof HumidityReadingTypeInterface) {
            $readingType[] = $this->sensorReadingTypeCreationFactory->getReadingTypeObjectBuilder(Humidity::getReadingTypeName())->buildReadingTypeObject($sensor);
        }

        if ($sensorType instanceof LatitudeReadingTypeInterface) {
            $readingType[] = $this->sensorReadingTypeCreationFactory->getReadingTypeObjectBuilder(Latitude::getReadingTypeName())->buildReadingTypeObject($sensor);
        }

        if ($sensorType instanceof AnalogReadingTypeInterface) {
            $readingType[] = $this->sensorReadingTypeCreationFactory->getReadingTypeObjectBuilder(Analog::getReadingTypeName())->buildReadingTypeObject($sensor);
        }

        if ($sensorType instanceof RelayReadingTypeInterface) {
            $readingType[] = $this->sensorReadingTypeCreationFactory->getReadingTypeObjectBuilder(Relay::getReadingTypeName())->buildReadingTypeObject($sensor);
        }

        if ($sensorType instanceof MotionSensorReadingTypeInterface) {
            $readingType[] = $this->sensorReadingTypeCreationFactory->getReadingTypeObjectBuilder(Motion::getReadingTypeName())->buildReadingTypeObject($sensor);
        }

        return $readingType ?? [];
    }
}
