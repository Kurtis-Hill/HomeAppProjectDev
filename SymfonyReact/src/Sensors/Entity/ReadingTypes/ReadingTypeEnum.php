<?php

namespace App\Sensors\Entity\ReadingTypes;

enum ReadingTypeEnum: string
{
    case Temperature = 'Temperature';

    case Humidity = 'Humidity';

    case Latitude = 'Latitude';

    case Analog = 'Analog';

    case RELAY = 'RELAY';

    case MOTION = 'MOTION';
}
