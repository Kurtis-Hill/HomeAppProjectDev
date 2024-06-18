<?php

namespace App\Factories\Sensor\SensorReadingType;

use App\Builders\Sensor\Internal\ReadingType\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder\AnalogStandardReadingTypeObjectBuilder;
use App\Builders\Sensor\Internal\ReadingType\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder\HumidityStandardReadingTypeObjectBuilder;
use App\Builders\Sensor\Internal\ReadingType\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder\LatitudeStandardReadingTypeObjectBuilder;
use App\Builders\Sensor\Internal\ReadingType\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder\MotionReadingTypeReadingTypeBuilder;
use App\Builders\Sensor\Internal\ReadingType\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder\ReadingTypeObjectBuilderInterface;
use App\Builders\Sensor\Internal\ReadingType\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder\RelayReadingTypeReadingTypeBuilder;
use App\Builders\Sensor\Internal\ReadingType\ReadingTypeCreationBuilders\ReadingTypeCreationBuilder\TemperatureStandardReadingTypeObjectBuilder;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Motion;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Relay;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Analog;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Humidity;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Latitude;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Temperature;
use App\Exceptions\Sensor\SensorReadingTypeRepositoryFactoryException;

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
