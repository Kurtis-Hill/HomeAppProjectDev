<?php

namespace App\Factories\Sensor\ReadingTypeFactories;

use App\Builders\Sensor\Internal\ReadingType\ReadingTypeCreationBuilders\SensorOutOfBoundsCreationBuilders\AnalogOutOfBoundsObjectCreationBuilder;
use App\Builders\Sensor\Internal\ReadingType\ReadingTypeCreationBuilders\SensorOutOfBoundsCreationBuilders\HumidityOutOfBoundsObjectCreationBuilder;
use App\Builders\Sensor\Internal\ReadingType\ReadingTypeCreationBuilders\SensorOutOfBoundsCreationBuilders\LatitudeOutOfBoundsObjectCreationBuilder;
use App\Builders\Sensor\Internal\ReadingType\ReadingTypeCreationBuilders\SensorOutOfBoundsCreationBuilders\OutOfBoundsObjectCreationBuilderInterface;
use App\Builders\Sensor\Internal\ReadingType\ReadingTypeCreationBuilders\SensorOutOfBoundsCreationBuilders\TemperatureOutOfBoundsObjectCreationBuilder;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Analog;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Humidity;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Latitude;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Temperature;

class OutOfBoundsEntityCreationFactory
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
    ) {
        $this->analogOutOfBoundsObjectCreationBuilder = $analogOutOfBoundsObjectCreationBuilder;
        $this->humidityOutOfBoundsObjectCreationBuilder = $humidityOutOfBoundsObjectCreationBuilder;
        $this->latitudeOutOfBoundsObjectCreationBuilder = $latitudeOutOfBoundsObjectCreationBuilder;
        $this->temperatureOutOfBoundsObjectCreationBuilder = $temperatureOutOfBoundsObjectCreationBuilder;
    }

    public function getConstRecordObjectBuilder(string $readingType): OutOfBoundsObjectCreationBuilderInterface
    {
        return match ($readingType) {
            Temperature::getReadingTypeName() => $this->temperatureOutOfBoundsObjectCreationBuilder,
            Humidity::getReadingTypeName() => $this->humidityOutOfBoundsObjectCreationBuilder,
            Latitude::getReadingTypeName() => $this->latitudeOutOfBoundsObjectCreationBuilder,
            Analog::getReadingTypeName() => $this->analogOutOfBoundsObjectCreationBuilder,
        };
    }
}
