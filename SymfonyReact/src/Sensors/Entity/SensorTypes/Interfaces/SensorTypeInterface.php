<?php

namespace App\Sensors\Entity\SensorTypes\Interfaces;

use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Motion;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Relay;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Temperature;
use App\Sensors\Entity\Sensor;
use Doctrine\Common\Collections\Collection;
use JetBrains\PhpStorm\ArrayShape;

interface SensorTypeInterface
{
    public static function getReadingTypeName(): string;
}
