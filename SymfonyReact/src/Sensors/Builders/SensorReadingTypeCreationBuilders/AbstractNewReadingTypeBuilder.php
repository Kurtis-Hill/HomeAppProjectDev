<?php

namespace App\Sensors\Builders\SensorReadingTypeCreationBuilders;

use App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder\AnalogReadingTypeObjectBuilder;
use App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder\HumidityReadingTypeObjectBuilder;
use App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder\LatitudeReadingTypeObjectBuilder;
use App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder\TemperatureReadingTypeObjectBuilder;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Temperature;
use App\Sensors\Entity\SensorTypes\Interfaces\AnalogReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\HumidityReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\LatitudeReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\TemperatureReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\StandardSensorTypeInterface;
use App\Sensors\Exceptions\SensorTypeException;

abstract class AbstractNewReadingTypeBuilder
{
    private TemperatureReadingTypeObjectBuilder $temperatureReadingTypeObjectBuilder;

    private HumidityReadingTypeObjectBuilder $humidityReadingTypeObjectBuilder;

    private LatitudeReadingTypeObjectBuilder $latitudeReadingTypeObjectBuilder;

    private AnalogReadingTypeObjectBuilder $analogReadingTypeObjectBuilder;

    public function __construct(
        TemperatureReadingTypeObjectBuilder $temperatureReadingTypeObjectBuilder,
        HumidityReadingTypeObjectBuilder $humidityReadingTypeObjectBuilder,
        LatitudeReadingTypeObjectBuilder $latitudeReadingTypeObjectBuilder,
        AnalogReadingTypeObjectBuilder $analogReadingTypeObjectBuilder,
    ) {
        $this->temperatureReadingTypeObjectBuilder = $temperatureReadingTypeObjectBuilder;
        $this->humidityReadingTypeObjectBuilder = $humidityReadingTypeObjectBuilder;
        $this->latitudeReadingTypeObjectBuilder = $latitudeReadingTypeObjectBuilder;
        $this->analogReadingTypeObjectBuilder = $analogReadingTypeObjectBuilder;
    }

    /**
     * @throws SensorTypeException
     */
    protected function buildStandardSensorReadingTypeObjects(StandardSensorTypeInterface $sensorType, array $currentReading = []): void
    {
        if (!$sensorType instanceof SensorTypeInterface) {
            throw new SensorTypeException('Sensor type must implement SensorTypeInterface');
        }
        if ($sensorType instanceof TemperatureReadingTypeInterface) {
            $this->temperatureReadingTypeObjectBuilder->buildReadingTypeObject($sensorType, $currentReading[Temperature::READING_TYPE] ?? 10);
        }

        if ($sensorType instanceof HumidityReadingTypeInterface) {
            $this->humidityReadingTypeObjectBuilder->buildReadingTypeObject($sensorType, $currentReading[Humidity::READING_TYPE] ?? 10);
        }

        if ($sensorType instanceof LatitudeReadingTypeInterface) {
            $this->latitudeReadingTypeObjectBuilder->buildReadingTypeObject($sensorType, $currentReading[Latitude::READING_TYPE] ?? 10);
        }

        if ($sensorType instanceof AnalogReadingTypeInterface) {
            $this->analogReadingTypeObjectBuilder->buildReadingTypeObject($sensorType, $currentReading[Analog::READING_TYPE] ?? 10);
        }
    }
}
