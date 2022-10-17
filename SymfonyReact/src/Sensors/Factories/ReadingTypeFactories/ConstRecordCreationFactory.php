<?php

namespace App\Sensors\Factories\ReadingTypeFactories;

use App\Sensors\Builders\ReadingTypeCreationBuilders\ConstRecordCreationBuilders\AnalogConstRecordObjectBuilder;
use App\Sensors\Builders\ReadingTypeCreationBuilders\ConstRecordCreationBuilders\ConstRecordObjectBuilderInterface;
use App\Sensors\Builders\ReadingTypeCreationBuilders\ConstRecordCreationBuilders\HumidityConstRecordObjectBuilder;
use App\Sensors\Builders\ReadingTypeCreationBuilders\ConstRecordCreationBuilders\LatitudeConstRecordObjectBuilder;
use App\Sensors\Builders\ReadingTypeCreationBuilders\ConstRecordCreationBuilders\TemperatureConstRecordObjectBuilder;
use App\Sensors\Entity\ReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\Temperature;

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
