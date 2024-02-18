<?php

namespace App\Sensors\Entity\ReadingTypes;

enum ReadingTypeEnum
{
    case Temperature;

    case Humidity;

    case Latitude;

    case Analog;
}
