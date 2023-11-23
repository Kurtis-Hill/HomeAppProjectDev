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
    public function setSensor(Sensor $sensor);

    public function getSensorTypeID(): int;

    public function getSensor(): Sensor;

    // make sure this returns the same data as in the seensortype table in the sensorType column
    public function getReadingTypeName(): string;

    public static function getReadingTypeAlias(): string;

    public static function getAllowedReadingTypes(): array;

    #[ArrayShape([Temperature::class|Humidity::class|Latitude::class|Motion::class|Analog::class|Relay::class])]
    public function getReadingTypes(): Collection;
}
