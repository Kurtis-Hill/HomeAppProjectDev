<?php

namespace App\Sensors\Factories\ReadingTypeQueryBuilderFactory;


use App\Sensors\Builders\Internal\ReadingType\ReadingTypeQueryDTOBuilders\AnalogQueryTypeDTOBuilder;
use App\Sensors\Builders\Internal\ReadingType\ReadingTypeQueryDTOBuilders\HumidityQueryTypeDTOBuilder;
use App\Sensors\Builders\Internal\ReadingType\ReadingTypeQueryDTOBuilders\LatitudeQueryTypeDTOBuilder;
use App\Sensors\Builders\Internal\ReadingType\ReadingTypeQueryDTOBuilders\MotionQueryTypeDTOBuilder;
use App\Sensors\Builders\Internal\ReadingType\ReadingTypeQueryDTOBuilders\ReadingTypeQueryDTOBuilderInterface;
use App\Sensors\Builders\Internal\ReadingType\ReadingTypeQueryDTOBuilders\RelayQueryTypeDTOBuilder;
use App\Sensors\Builders\Internal\ReadingType\ReadingTypeQueryDTOBuilders\TemperatureQueryTypeDTOBuilder;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Motion;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Relay;
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
     * @throws ReadingTypeBuilderFailureException
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
