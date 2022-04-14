<?php

namespace App\Sensors\Factories\ReadingTypeQueryBuilderFactory;


use App\Sensors\Entity\ReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\Temperature;
use App\Sensors\Builders\ReadingTypeQueryDTOBuilders\AnalogQueryTypeDTOBuilder;
use App\Sensors\Builders\ReadingTypeQueryDTOBuilders\ReadingTypeQueryDTOBuilderInterface;
use App\Sensors\Builders\ReadingTypeQueryDTOBuilders\HumidityQueryTypeDTOBuilder;
use App\Sensors\Builders\ReadingTypeQueryDTOBuilders\LatitudeQueryTypeDTOBuilder;
use App\Sensors\Builders\ReadingTypeQueryDTOBuilders\TemperatureQueryTypeDTOBuilder;
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
