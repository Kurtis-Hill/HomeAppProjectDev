<?php

namespace App\Sensors\Builders\SensorReadingTypeCreationBuilders;

use App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder\AnalogReadingTypeObjectBuilder;
use App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder\HumidityReadingTypeObjectBuilder;
use App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder\LatitudeReadingTypeObjectBuilder;
use App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder\TemperatureReadingTypeObjectBuilder;
use App\Sensors\Entity\SensorTypes\Interfaces\AnalogSensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\HumiditySensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\LatitudeSensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\StandardSensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\TemperatureSensorTypeInterface;
use App\Sensors\Exceptions\SensorTypeException;

class AbstractNewReadingTypeBuilder
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
    protected function buildStandardSensorReadingTypeObjects(StandardSensorTypeInterface $sensorType, float|int $currentReading): void
    {
        if (!$sensorType instanceof SensorTypeInterface) {
            throw new SensorTypeException('Sensor type must implement SensorTypeInterface');
        }
        if ($sensorType instanceof TemperatureSensorTypeInterface) {
            $this->temperatureReadingTypeObjectBuilder->buildReadingTypeObject($sensorType, $currentReading);
        }

        if ($sensorType instanceof HumiditySensorTypeInterface) {
            $this->humidityReadingTypeObjectBuilder->buildReadingTypeObject($sensorType, $currentReading);
        }

        if ($sensorType instanceof LatitudeSensorTypeInterface) {
            $this->latitudeReadingTypeObjectBuilder->buildReadingTypeObject($sensorType, $currentReading);
        }

        if ($sensorType instanceof AnalogSensorTypeInterface) {
            $this->analogReadingTypeObjectBuilder->buildReadingTypeObject($sensorType, $currentReading);
        }
    }
}
