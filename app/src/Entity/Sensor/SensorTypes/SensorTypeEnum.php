<?php

namespace App\Entity\Sensor\SensorTypes;

enum SensorTypeEnum
{
    case Soil;

    case Dht;

    case Dallas;

    case Bmp;

    case GenericRelay;

    case GenericMotion;
}
