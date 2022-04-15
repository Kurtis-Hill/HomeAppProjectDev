<?php

namespace App\Sensors\Factories\ReadingTypeFactories;

use App\Sensors\Builders\ReadingTypeCreationBuilders\NewConstRecordCreationBuilders\AnalogConstRecordObjectBuilder;
use App\Sensors\Builders\ReadingTypeCreationBuilders\NewConstRecordCreationBuilders\ConstRecordObjectBuilderInterface;
use App\Sensors\Builders\ReadingTypeCreationBuilders\NewConstRecordCreationBuilders\HumidityConstRecordObjectBuilder;
use App\Sensors\Builders\ReadingTypeCreationBuilders\NewConstRecordCreationBuilders\LatitudeConstRecordObjectBuilder;
use App\Sensors\Builders\ReadingTypeCreationBuilders\NewConstRecordCreationBuilders\TemperatureConstRecordObjectBuilder;
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
    )
    {
        $this->analogConstRecordObjectBuilder = $analogConstRecordObjectBuilder;
        $this->humidityConstRecordObjectBuilder = $humidityConstRecordObjectBuilder;
        $this->latitudeConstRecordObjectBuilder = $latitudeConstRecordObjectBuilder;
        $this->temperatureConstRecordObjectBuilder = $temperatureConstRecordObjectBuilder;
    }

    public function getConstRecordObjectBuilder(string $readingType): ConstRecordObjectBuilderInterface
    {
        return match ($readingType) {
            Temperature::READING_TYPE => $this->temperatureConstRecordObjectBuilder,
            Latitude::READING_TYPE => $this->latitudeConstRecordObjectBuilder,
            Humidity::READING_TYPE => $this->humidityConstRecordObjectBuilder,
            Analog::READING_TYPE => $this->analogConstRecordObjectBuilder,
        };
    }
}