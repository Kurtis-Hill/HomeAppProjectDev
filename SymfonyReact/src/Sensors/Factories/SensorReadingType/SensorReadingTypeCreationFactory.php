<?php

namespace App\Sensors\Factories\SensorReadingType;

use App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder\AnalogStandardReadingTypeObjectBuilder;
use App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder\HumidityStandardReadingTypeObjectBuilder;
use App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder\LatitudeStandardReadingTypeObjectBuilder;
use App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder\MotionReadingTypeReadingTypeBuilder;
use App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder\ReadingTypeObjectBuilderInterface;
use App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder\RelayReadingTypeReadingTypeBuilder;
use App\Sensors\Builders\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder\TemperatureStandardReadingTypeObjectBuilder;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Motion;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Relay;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Temperature;
use App\Sensors\Exceptions\SensorReadingTypeRepositoryFactoryException;

readonly class SensorReadingTypeCreationFactory
{
    public function __construct(
        private TemperatureStandardReadingTypeObjectBuilder $temperatureStandardReadingTypeObjectBuilder,
        private HumidityStandardReadingTypeObjectBuilder $humidityStandardReadingTypeObjectBuilder,
        private LatitudeStandardReadingTypeObjectBuilder $latitudeStandardReadingTypeObjectBuilder,
        private AnalogStandardReadingTypeObjectBuilder $analogStandardReadingTypeObjectBuilder,
        private MotionReadingTypeReadingTypeBuilder $motionReadingTypeReadingTypeBuilder,
        private RelayReadingTypeReadingTypeBuilder $relayReadingTypeReadingTypeBuilder
    ) {}

    /**
     * @throws SensorReadingTypeRepositoryFactoryException
     */
    public function getReadingTypeObjectBuilder(string $readingType): ReadingTypeObjectBuilderInterface
    {
        return match ($readingType) {
            Temperature::getReadingTypeName() => $this->temperatureStandardReadingTypeObjectBuilder,
            Humidity::getReadingTypeName() => $this->humidityStandardReadingTypeObjectBuilder,
            Latitude::getReadingTypeName() => $this->latitudeStandardReadingTypeObjectBuilder,
            Analog::getReadingTypeName() => $this->analogStandardReadingTypeObjectBuilder,
            Motion::getReadingTypeName() => $this->motionReadingTypeReadingTypeBuilder,
            Relay::getReadingTypeName() => $this->relayReadingTypeReadingTypeBuilder,
            default => throw new SensorReadingTypeRepositoryFactoryException(
                sprintf(
                    SensorReadingTypeRepositoryFactoryException::READING_TYPE_NOT_FOUND,
                    $readingType,
                )
            )
        };
    }
}
