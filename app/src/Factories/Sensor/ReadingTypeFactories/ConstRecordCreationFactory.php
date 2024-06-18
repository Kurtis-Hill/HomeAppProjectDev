<?php

namespace App\Factories\Sensor\ReadingTypeFactories;

use App\Builders\Sensor\Internal\ReadingType\ReadingTypeCreationBuilders\ConstRecordCreationBuilders\AnalogConstRecordObjectBuilder;
use App\Builders\Sensor\Internal\ReadingType\ReadingTypeCreationBuilders\ConstRecordCreationBuilders\ConstRecordObjectBuilderInterface;
use App\Builders\Sensor\Internal\ReadingType\ReadingTypeCreationBuilders\ConstRecordCreationBuilders\HumidityConstRecordObjectBuilder;
use App\Builders\Sensor\Internal\ReadingType\ReadingTypeCreationBuilders\ConstRecordCreationBuilders\LatitudeConstRecordObjectBuilder;
use App\Builders\Sensor\Internal\ReadingType\ReadingTypeCreationBuilders\ConstRecordCreationBuilders\TemperatureConstRecordObjectBuilder;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Analog;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Humidity;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Latitude;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Temperature;

class ConstRecordCreationFactory
{
    private AnalogConstRecordObjectBuilder $analogConstRecordObjectBuilder;

    private HumidityConstRecordObjectBuilder $humidityConstRecordObjectBuilder;

    private LatitudeConstRecordObjectBuilder $latitudeConstRecordObjectBuilder;

    private TemperatureConstRecordObjectBuilder $temperatureConstRecordObjectBuilder;

    public function __construct(
        AnalogConstRecordObjectBuilder $analogConstRecordObjectBuilder,
        HumidityConstRecordObjectBuilder $humidityConstRecordObjectBuilder,
        LatitudeConstRecordObjectBuilder $latitudeConstRecordObjectBuilder,
        TemperatureConstRecordObjectBuilder $temperatureConstRecordObjectBuilder
    ) {
        $this->analogConstRecordObjectBuilder = $analogConstRecordObjectBuilder;
        $this->humidityConstRecordObjectBuilder = $humidityConstRecordObjectBuilder;
        $this->latitudeConstRecordObjectBuilder = $latitudeConstRecordObjectBuilder;
        $this->temperatureConstRecordObjectBuilder = $temperatureConstRecordObjectBuilder;
    }

    public function getConstRecordObjectBuilder(string $readingType): ConstRecordObjectBuilderInterface
    {
        return match ($readingType) {
            Temperature::getReadingTypeName() => $this->temperatureConstRecordObjectBuilder,
            Latitude::getReadingTypeName() => $this->latitudeConstRecordObjectBuilder,
            Humidity::getReadingTypeName() => $this->humidityConstRecordObjectBuilder,
            Analog::getReadingTypeName() => $this->analogConstRecordObjectBuilder,
        };
    }
}
