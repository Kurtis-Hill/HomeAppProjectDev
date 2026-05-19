<?php

namespace App\DTOs\Sensor\Request\SensorUpdateDTO;

use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Motion;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Relay;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Analog;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Humidity;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Latitude;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Temperature;
use Symfony\Component\Serializer\Attribute\DiscriminatorMap;

#[DiscriminatorMap(
    typeProperty: 'readingType',
    mapping: [
        Temperature::READING_TYPE => StandardSensorUpdateBoundaryDataDTO::class,
        Humidity::READING_TYPE    => StandardSensorUpdateBoundaryDataDTO::class,
        Analog::READING_TYPE      => StandardSensorUpdateBoundaryDataDTO::class,
        Latitude::READING_TYPE    => StandardSensorUpdateBoundaryDataDTO::class,
        Relay::READING_TYPE       => BoolSensorUpdateBoundaryDataDTO::class,
        Motion::READING_TYPE      => BoolSensorUpdateBoundaryDataDTO::class,
    ]
)]
interface SensorUpdateBoundaryDataDTOInterface
{
    public function getReadingType(): mixed;

    public function getConstRecord(): mixed;
}
