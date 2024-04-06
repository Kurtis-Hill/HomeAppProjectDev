<?php

namespace App\Sensors\Builders\Internal\ReadingType\ReadingTypeCreationBuilders\SensorReadingTypeCreationBuilders;

use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Motion;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Relay;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Temperature;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Interfaces\AnalogReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\HumidityReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\LatitudeReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\MotionSensorReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\RelayReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\TemperatureReadingTypeInterface;
use App\Sensors\Exceptions\SensorReadingTypeRepositoryFactoryException;
use App\Sensors\Exceptions\SensorTypeException;
use App\Sensors\Factories\SensorReadingType\SensorReadingTypeCreationFactory;
use Doctrine\ORM\Exception\ORMException;
use JetBrains\PhpStorm\ArrayShape;

readonly abstract class AbstractNewReadingTypeBuilder
{
    public function __construct(
        private SensorReadingTypeCreationFactory $sensorReadingTypeCreationFactory,
    ) {}

    /**
     * @throws SensorReadingTypeRepositoryFactoryException
     * @throws SensorTypeException
     * @throws ORMException
     */
    #[ArrayShape([Temperature::class|Humidity::class|Latitude::class|Analog::class|Relay::class|Motion::class])]
    public function buildNewSensorTypeObjects(Sensor $sensor): array
    {
        return $this->buildSensorReadingTypeObjects($sensor);
    }

    /**
     * @throws SensorTypeException
     * @throws SensorReadingTypeRepositoryFactoryException
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
