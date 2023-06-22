<?php

namespace App\Sensors\Factories\ReadingTypeQueryBuilderFactory;


use App\Sensors\Builders\ReadingTypeQueryDTOBuilders\AnalogQueryTypeDTOBuilder;
use App\Sensors\Builders\ReadingTypeQueryDTOBuilders\HumidityQueryTypeDTOBuilder;
use App\Sensors\Builders\ReadingTypeQueryDTOBuilders\LatitudeQueryTypeDTOBuilder;
use App\Sensors\Builders\ReadingTypeQueryDTOBuilders\ReadingTypeQueryDTOBuilderInterface;
use App\Sensors\Builders\ReadingTypeQueryDTOBuilders\TemperatureQueryTypeDTOBuilder;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Temperature;
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
            Temperature::getReadingTypeName() => $this->temperatureQueryTypeDTOBuilder,
            Humidity::getReadingTypeName() => $this->humidityQueryTypeDTOBuilder,
            Latitude::getReadingTypeName() => $this->latitudeQueryTypeDTOBuilder,
            Analog::getReadingTypeName() => $this->analogQueryTypeDTOBuilder,
            default => throw new ReadingTypeBuilderFailureException('Unknown reading type'),
        };
    }
}
