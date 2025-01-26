<?php

namespace App\Entity\Sensor\ReadingTypes;

enum ReadingTypeEnum: string
{
    case Temperature = 'Temperature';

    case Humidity = 'Humidity';

    case Latitude = 'Latitude';

    case Analog = 'Analog';

    case Relay = 'RELAY';

    case Motion = 'MOTION';
}
