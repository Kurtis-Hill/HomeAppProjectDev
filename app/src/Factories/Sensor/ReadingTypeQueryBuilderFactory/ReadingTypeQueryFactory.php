<?php

namespace App\Factories\Sensor\ReadingTypeQueryBuilderFactory;


use App\Builders\Sensor\Internal\ReadingType\ReadingTypeQueryDTOBuilders\AnalogQueryTypeDTOBuilder;
use App\Builders\Sensor\Internal\ReadingType\ReadingTypeQueryDTOBuilders\HumidityQueryTypeDTOBuilder;
use App\Builders\Sensor\Internal\ReadingType\ReadingTypeQueryDTOBuilders\LatitudeQueryTypeDTOBuilder;
use App\Builders\Sensor\Internal\ReadingType\ReadingTypeQueryDTOBuilders\MotionQueryTypeDTOBuilder;
use App\Builders\Sensor\Internal\ReadingType\ReadingTypeQueryDTOBuilders\ReadingTypeQueryDTOBuilderInterface;
use App\Builders\Sensor\Internal\ReadingType\ReadingTypeQueryDTOBuilders\RelayQueryTypeDTOBuilder;
use App\Builders\Sensor\Internal\ReadingType\ReadingTypeQueryDTOBuilders\TemperatureQueryTypeDTOBuilder;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Motion;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Relay;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Analog;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Humidity;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Latitude;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Temperature;
use App\Exceptions\UserInterface\ReadingTypeBuilderFailureException;

class ReadingTypeQueryFactory
{
    private TemperatureQueryTypeDTOBuilder $temperatureQueryTypeDTOBuilder;

    private HumidityQueryTypeDTOBuilder $humidityQueryTypeDTOBuilder;

    private AnalogQueryTypeDTOBuilder $analogQueryTypeDTOBuilder;

    private LatitudeQueryTypeDTOBuilder $latitudeQueryTypeDTOBuilder;

    private MotionQueryTypeDTOBuilder $motionQueryTypeDTOBuilder;

    private RelayQueryTypeDTOBuilder $relayQueryTypeDTOBuilder;

    public function __construct(
        TemperatureQueryTypeDTOBuilder $temperatureQueryTypeDTOBuilder,
        HumidityQueryTypeDTOBuilder $humidityQueryTypeDTOBuilder,
        LatitudeQueryTypeDTOBuilder $latitudeQueryTypeDTOBuilder,
        AnalogQueryTypeDTOBuilder $analogQueryTypeDTOBuilder,
        MotionQueryTypeDTOBuilder $motionQueryTypeDTOBuilder,
        RelayQueryTypeDTOBuilder $relayQueryTypeDTOBuilder,
    ) {
        $this->temperatureQueryTypeDTOBuilder = $temperatureQueryTypeDTOBuilder;
        $this->humidityQueryTypeDTOBuilder = $humidityQueryTypeDTOBuilder;
        $this->latitudeQueryTypeDTOBuilder = $latitudeQueryTypeDTOBuilder;
        $this->analogQueryTypeDTOBuilder = $analogQueryTypeDTOBuilder;
        $this->motionQueryTypeDTOBuilder = $motionQueryTypeDTOBuilder;
        $this->relayQueryTypeDTOBuilder = $relayQueryTypeDTOBuilder;
    }

    /**
     * @throws \App\Exceptions\UserInterface\ReadingTypeBuilderFailureException
     */
    public function getReadingTypeQueryDTOBuilder(string $readingType): ReadingTypeQueryDTOBuilderInterface
    {
        return match ($readingType) {
            Temperature::getReadingTypeName() => $this->temperatureQueryTypeDTOBuilder,
            Humidity::getReadingTypeName() => $this->humidityQueryTypeDTOBuilder,
            Latitude::getReadingTypeName() => $this->latitudeQueryTypeDTOBuilder,
            Analog::getReadingTypeName() => $this->analogQueryTypeDTOBuilder,
            Motion::getReadingTypeName() => $this->motionQueryTypeDTOBuilder,
            Relay::getReadingTypeName() => $this->relayQueryTypeDTOBuilder,
            default => throw new ReadingTypeBuilderFailureException('Unknown reading type'),
        };
    }
}
