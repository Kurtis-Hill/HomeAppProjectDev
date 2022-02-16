<?php

namespace App\ESPDeviceSensor\Factories\ReadingTypeFactories;

use App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders\NewConstRecordCreationBuilders\AnalogConstRecordObjectBuilder;
use App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders\NewConstRecordCreationBuilders\ConstRecordObjectBuilderInterface;
use App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders\NewConstRecordCreationBuilders\HumidityConstRecordObjectBuilder;
use App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders\NewConstRecordCreationBuilders\LatitudeConstRecordObjectBuilder;
use App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders\NewConstRecordCreationBuilders\TemperatureConstRecordObjectBuilder;
use App\ESPDeviceSensor\Entity\ReadingTypes\Analog;
use App\ESPDeviceSensor\Entity\ReadingTypes\Humidity;
use App\ESPDeviceSensor\Entity\ReadingTypes\Latitude;
use App\ESPDeviceSensor\Entity\ReadingTypes\Temperature;

//@TODO add latutude
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
