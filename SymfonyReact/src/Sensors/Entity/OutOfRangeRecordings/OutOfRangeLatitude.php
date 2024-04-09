<?php

namespace App\Sensors\Entity\OutOfRangeRecordings;

use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\StandardReadingSensorInterface;
use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\LatitudeConstraint;
use App\Sensors\Repository\OutOfBounds\ORM\OutOfBoundsLatitudeRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity(repositoryClass: OutOfBoundsLatitudeRepository::class),
]
class OutOfRangeLatitude extends AbstractOutOfRange
{
    #[LatitudeConstraint]
    protected int|float $sensorReading;
}
