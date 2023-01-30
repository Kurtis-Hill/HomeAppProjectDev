<?php

namespace App\Sensors\Entity\SensorTypes;

enum SensorTypeEnum
{
    case Soil;

    case Dht;

    case Dallas;

    case Bmp;
}
