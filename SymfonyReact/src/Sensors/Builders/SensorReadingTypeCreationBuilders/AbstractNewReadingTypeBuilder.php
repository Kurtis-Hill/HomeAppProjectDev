<?php

namespace App\Sensors\Builders\SensorReadingTypeCreationBuilders;

use App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder\AnalogStandardReadingTypeObjectBuilder;
use App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder\HumidityStandardReadingTypeObjectBuilder;
use App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder\LatitudeStandardReadingTypeObjectBuilder;
use App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder\MotionReadingTypeReadingTypeBuilder;
use App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder\RelayReadingTypeReadingTypeBuilder;
use App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder\TemperatureStandardReadingTypeObjectBuilder;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Motion;
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
use App\Sensors\Exceptions\SensorTypeException;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Relay;

abstract class AbstractNewReadingTypeBuilder
{
    private TemperatureStandardReadingTypeObjectBuilder $temperatureReadingTypeObjectBuilder;

    private HumidityStandardReadingTypeObjectBuilder $humidityReadingTypeObjectBuilder;

    private LatitudeStandardReadingTypeObjectBuilder $latitudeReadingTypeObjectBuilder;

    private AnalogStandardReadingTypeObjectBuilder $analogReadingTypeObjectBuilder;

    private RelayReadingTypeReadingTypeBuilder $relayReadingTypeObjectBuilder;

    private MotionReadingTypeReadingTypeBuilder $motionReadingTypeObjectBuilder;

    public function __construct(
        TemperatureStandardReadingTypeObjectBuilder $temperatureReadingTypeObjectBuilder,
        HumidityStandardReadingTypeObjectBuilder $humidityReadingTypeObjectBuilder,
        LatitudeStandardReadingTypeObjectBuilder $latitudeReadingTypeObjectBuilder,
        AnalogStandardReadingTypeObjectBuilder $analogReadingTypeObjectBuilder,
        RelayReadingTypeReadingTypeBuilder $relayReadingTypeObjectBuilder,
        MotionReadingTypeReadingTypeBuilder $motionReadingTypeObjectBuilder,
    ) {
        $this->temperatureReadingTypeObjectBuilder = $temperatureReadingTypeObjectBuilder;
        $this->humidityReadingTypeObjectBuilder = $humidityReadingTypeObjectBuilder;
        $this->latitudeReadingTypeObjectBuilder = $latitudeReadingTypeObjectBuilder;
        $this->analogReadingTypeObjectBuilder = $analogReadingTypeObjectBuilder;
        $this->relayReadingTypeObjectBuilder = $relayReadingTypeObjectBuilder;
        $this->motionReadingTypeObjectBuilder = $motionReadingTypeObjectBuilder;
    }

    /**
     * @throws SensorTypeException
     */
    protected function buildSensorReadingTypeObjects(Sensor $sensor): void
    {
        if (!$sensor instanceof SensorTypeInterface) {
            throw new SensorTypeException('Sensor type must implement SensorTypeInterface');
        }
        if ($sensor instanceof TemperatureReadingTypeInterface) {
            $this->temperatureReadingTypeObjectBuilder->buildReadingTypeObject($sensor);
        }

        if ($sensor instanceof HumidityReadingTypeInterface) {
            $this->humidityReadingTypeObjectBuilder->buildReadingTypeObject($sensor);
        }

        if ($sensor instanceof LatitudeReadingTypeInterface) {
            $this->latitudeReadingTypeObjectBuilder->buildReadingTypeObject($sensor);
        }

        if ($sensor instanceof AnalogReadingTypeInterface) {
            $this->analogReadingTypeObjectBuilder->buildReadingTypeObject($sensor);
        }

        if ($sensor instanceof RelayReadingTypeInterface) {
            $this->relayReadingTypeObjectBuilder->buildReadingTypeObject($sensor);
        }

        if ($sensor instanceof MotionSensorReadingTypeInterface) {
            $this->motionReadingTypeObjectBuilder->buildReadingTypeObject($sensor, $currentReading[Motion::READING_TYPE] ?? false);
        }
    }
}
