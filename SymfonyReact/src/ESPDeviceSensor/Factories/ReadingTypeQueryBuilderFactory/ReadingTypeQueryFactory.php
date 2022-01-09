<?php

namespace App\ESPDeviceSensor\Factories\ReadingTypeQueryBuilderFactory;


use App\ESPDeviceSensor\Entity\ReadingTypes\Analog;
use App\ESPDeviceSensor\Entity\ReadingTypes\Humidity;
use App\ESPDeviceSensor\Entity\ReadingTypes\Latitude;
use App\ESPDeviceSensor\Entity\ReadingTypes\Temperature;
use App\ESPDeviceSensor\Builders\ReadingTypeQueryDTOBuilders\AnalogQueryTypeDTOBuilder;
use App\ESPDeviceSensor\Builders\ReadingTypeQueryDTOBuilders\ReadingTypeQueryDTOBuilderInterface;
use App\ESPDeviceSensor\Builders\ReadingTypeQueryDTOBuilders\HumidityQueryTypeDTOBuilder;
use App\ESPDeviceSensor\Builders\ReadingTypeQueryDTOBuilders\LatitudeQueryTypeDTOBuilder;
use App\ESPDeviceSensor\Builders\ReadingTypeQueryDTOBuilders\TemperatureQueryTypeDTOBuilder;
use App\UserInterface\Exceptions\ReadingTypeBuilderFailureException;

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
