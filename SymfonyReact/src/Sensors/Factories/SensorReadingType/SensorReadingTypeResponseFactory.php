<?php

namespace App\Sensors\Factories\SensorReadingType;

use App\Sensors\Builders\Response\SensorReadingTypeResponseBuilders\Bool\MotionResponseDTOBuilder;
use App\Sensors\Builders\Response\SensorReadingTypeResponseBuilders\Bool\RelayResponseDTOBuilder;
use App\Sensors\Builders\Response\SensorReadingTypeResponseBuilders\SensorResponseDTOBuilderInterface;
use App\Sensors\Builders\Response\SensorReadingTypeResponseBuilders\Standard\AnalogResponseDTOBuilder;
use App\Sensors\Builders\Response\SensorReadingTypeResponseBuilders\Standard\HumidityResponseDTOBuilder;
use App\Sensors\Builders\Response\SensorReadingTypeResponseBuilders\Standard\LatitudeResponseDTOBuilder;
use App\Sensors\Builders\Response\SensorReadingTypeResponseBuilders\Standard\TemperatureResponseDTOBuilder;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Motion;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Relay;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Temperature;
use App\Sensors\Exceptions\SensorReadingTypeObjectNotFoundException;
use App\Sensors\Exceptions\SensorReadingTypeRepositoryFactoryException;

class SensorReadingTypeResponseFactory
{
    private AnalogResponseDTOBuilder $analogResponseDTOBuilder;

    private HumidityResponseDTOBuilder $humidityResponseBuilder;

    private TemperatureResponseDTOBuilder $temperatureResponseBuilder;

    private LatitudeResponseDTOBuilder $latitudeResponseBuilder;

    private MotionResponseDTOBuilder $motionResponseBuilder;

    private RelayResponseDTOBuilder $relayResponseBuilder;

    public function __construct(
        AnalogResponseDTOBuilder $analogResponseDTOBuilder,
        HumidityResponseDTOBuilder $humidityResponseBuilder,
        TemperatureResponseDTOBuilder $temperatureResponseBuilder,
        LatitudeResponseDTOBuilder $latitudeResponseBuilder,
        MotionResponseDTOBuilder $motionResponseBuilder,
        RelayResponseDTOBuilder $relayResponseBuilder
    ) {
        $this->analogResponseDTOBuilder = $analogResponseDTOBuilder;
        $this->humidityResponseBuilder = $humidityResponseBuilder;
        $this->temperatureResponseBuilder = $temperatureResponseBuilder;
        $this->latitudeResponseBuilder = $latitudeResponseBuilder;
        $this->motionResponseBuilder = $motionResponseBuilder;
        $this->relayResponseBuilder = $relayResponseBuilder;
    }

    /**
     * @throws SensorReadingTypeRepositoryFactoryException|SensorReadingTypeObjectNotFoundException
     */
    public function getSensorReadingTypeDTOResponseBuilder(string $readingType): SensorResponseDTOBuilderInterface
    {
        return match ($readingType) {
            Analog::getReadingTypeName() => $this->analogResponseDTOBuilder,
            Humidity::getReadingTypeName() => $this->humidityResponseBuilder,
            Temperature::getReadingTypeName() => $this->temperatureResponseBuilder,
            Latitude::getReadingTypeName() => $this->latitudeResponseBuilder,
            Motion::getReadingTypeName() => $this->motionResponseBuilder,
            Relay::getReadingTypeName() => $this->relayResponseBuilder,
            default => throw new SensorReadingTypeObjectNotFoundException(
                sprintf(
                    SensorReadingTypeRepositoryFactoryException::READING_TYPE_NOT_FOUND,
                    $readingType
                )
            )
        };
    }
}
