<?php

namespace App\ESPDeviceSensor\Factories\ReadingTypeFactories;

use App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders\NewSensorOutOfBoundsCreationBuilders\AnalogOutOfBoundsObjectCreationBuilder;
use App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders\NewSensorOutOfBoundsCreationBuilders\HumidityOutOfBoundsObjectCreationBuilder;
use App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders\NewSensorOutOfBoundsCreationBuilders\LatitudeOutOfBoundsObjectCreationBuilder;
use App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders\NewSensorOutOfBoundsCreationBuilders\OutOfBoundsObjectCreationBuilderInterface;
use App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders\NewSensorOutOfBoundsCreationBuilders\TemperatureOutOfBoundsObjectCreationBuilder;
use App\ESPDeviceSensor\Entity\ReadingTypes\Analog;
use App\ESPDeviceSensor\Entity\ReadingTypes\Humidity;
use App\ESPDeviceSensor\Entity\ReadingTypes\Latitude;
use App\ESPDeviceSensor\Entity\ReadingTypes\Temperature;
//@TODO add latutude
class OutOfBoundsCreationFactory
{
    private AnalogOutOfBoundsObjectCreationBuilder $analogOutOfBoundsObjectCreationBuilder;

    private HumidityOutOfBoundsObjectCreationBuilder $humidityOutOfBoundsObjectCreationBuilder;

    private LatitudeOutOfBoundsObjectCreationBuilder $latitudeOutOfBoundsObjectCreationBuilder;

    private TemperatureOutOfBoundsObjectCreationBuilder $temperatureOutOfBoundsObjectCreationBuilder;

    public function __construct(
        AnalogOutOfBoundsObjectCreationBuilder $analogOutOfBoundsObjectCreationBuilder,
        HumidityOutOfBoundsObjectCreationBuilder $humidityOutOfBoundsObjectCreationBuilder,
        LatitudeOutOfBoundsObjectCreationBuilder $latitudeOutOfBoundsObjectCreationBuilder,
        TemperatureOutOfBoundsObjectCreationBuilder $temperatureOutOfBoundsObjectCreationBuilder,
    )
    {
        $this->analogOutOfBoundsObjectCreationBuilder = $analogOutOfBoundsObjectCreationBuilder;
        $this->humidityOutOfBoundsObjectCreationBuilder = $humidityOutOfBoundsObjectCreationBuilder;
        $this->latitudeOutOfBoundsObjectCreationBuilder = $latitudeOutOfBoundsObjectCreationBuilder;
        $this->temperatureOutOfBoundsObjectCreationBuilder = $temperatureOutOfBoundsObjectCreationBuilder;
    }

    public function getConstRecordObjectBuilder(string $readingType): OutOfBoundsObjectCreationBuilderInterface
    {
        return match ($readingType) {
            Temperature::READING_TYPE => $this->temperatureOutOfBoundsObjectCreationBuilder,
            Humidity::READING_TYPE => $this->humidityOutOfBoundsObjectCreationBuilder,
            Latitude::READING_TYPE => $this->latitudeOutOfBoundsObjectCreationBuilder,
            Analog::READING_TYPE => $this->analogOutOfBoundsObjectCreationBuilder,
        };
    }
}
