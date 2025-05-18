<?php

namespace App\Entity\Sensor\SensorTypes;

use App\Traits\EnumToArrayTrait;

enum SensorTypeEnum: string
{
    use EnumToArrayTrait;

    case Soil = Soil::NAME;

    case Dht = Dht::NAME;

    case Dallas = Dallas::NAME;

    case Bmp = Bmp::NAME;

    case GenericRelay = GenericRelay::NAME;

    case GenericMotion = GenericMotion::NAME;

    case LDR = LDR::NAME;

    case SHT = Sht::NAME;
}
