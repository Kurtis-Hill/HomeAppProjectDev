<?php

namespace App\Entity\Sensor\ReadingTypes;

use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Motion;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Relay;
use App\Entity\Sensor\ReadingTypes\LEDReadingTypes\WS2812B;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Analog;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Humidity;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Latitude;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Temperature;
use App\Traits\EnumToArrayTrait;

enum ReadingTypeEnum: string
{
    use EnumToArrayTrait;

    case Temperature = Temperature::READING_TYPE;

    case Humidity = Humidity::READING_TYPE;

    case Latitude = Latitude::READING_TYPE;

    case Analog = Analog::READING_TYPE;

    case Relay = Relay::READING_TYPE;

    case Motion = Motion::READING_TYPE;

    case WS2812B = WS2812B::READING_TYPE;
}
