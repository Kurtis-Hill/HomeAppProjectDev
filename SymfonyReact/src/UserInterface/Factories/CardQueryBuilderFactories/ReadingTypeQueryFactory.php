<?php

namespace App\UserInterface\Factories\CardQueryBuilderFactories;

use App\ESPDeviceSensor\Entity\ReadingTypes\Analog;
use App\ESPDeviceSensor\Entity\ReadingTypes\Humidity;
use App\ESPDeviceSensor\Entity\ReadingTypes\Latitude;
use App\ESPDeviceSensor\Entity\ReadingTypes\Temperature;
use App\UserInterface\Builders\CardReadingTypeQueryDTOBuilder\AnalogQueryTypeDTOBuilder;
use App\UserInterface\Builders\CardReadingTypeQueryDTOBuilder\ReadingTypeQueryDTOBuilderInterface;
use App\UserInterface\Builders\CardReadingTypeQueryDTOBuilder\HumidityQueryTypeDTOBuilder;
use App\UserInterface\Builders\CardReadingTypeQueryDTOBuilder\LatitudeQueryTypeDTOBuilder;
use App\UserInterface\Builders\CardReadingTypeQueryDTOBuilder\TemperatureQueryTypeDTOBuilder;
use App\UserInterface\Exceptions\ReadingTypeBuilderFailureException;
//@TODO move these to sensors namespace
class ReadingTypeQueryFactory
{
    private TemperatureQueryTypeDTOBuilder $temperatureQueryTypeDTOBuilder;

    private HumidityQueryTypeDTOBuilder $humidityQueryTypeDTOBuilder;

    private AnalogQueryTypeDTOBuilder $analogQueryTypeDTOBuilder;

    private LatitudeQueryTypeDTOBuilder $latitudeQueryTypeDTOBuilder;

    public function __construct(
        TemperatureQueryTypeDTOBuilder $temperatureQueryTypeDTOBuilder,
        HumidityQueryTypeDTOBuilder $humidityQueryTypeDTOBuilder,
        LatitudeQueryTypeDTOBuilder $latitudeQueryTypeDTOBuilder,
        AnalogQueryTypeDTOBuilder $analogQueryTypeDTOBuilder,
    ) {
        $this->temperatureQueryTypeDTOBuilder = $temperatureQueryTypeDTOBuilder;
        $this->humidityQueryTypeDTOBuilder = $humidityQueryTypeDTOBuilder;
        $this->latitudeQueryTypeDTOBuilder = $latitudeQueryTypeDTOBuilder;
        $this->analogQueryTypeDTOBuilder = $analogQueryTypeDTOBuilder;
    }

    /**
     * @throws ReadingTypeBuilderFailureException
     */
    public function getReadingTypeQueryDTOBuilder(string $readingType): ReadingTypeQueryDTOBuilderInterface
    {
        return match ($readingType) {
            Temperature::READING_TYPE => $this->temperatureQueryTypeDTOBuilder,
            Humidity::READING_TYPE => $this->humidityQueryTypeDTOBuilder,
            Latitude::READING_TYPE => $this->latitudeQueryTypeDTOBuilder,
            Analog::READING_TYPE => $this->analogQueryTypeDTOBuilder,
            default => throw new ReadingTypeBuilderFailureException('Unknown reading type'),
        };
    }
}
