<?php

namespace App\Entity\Sensor\OutOfRangeRecordings;

use App\Repository\Sensor\OutOfBounds\ORM\OutOfBoundsLatitudeRepository;
use App\CustomValidators\Sensor\SensorDataValidators\LatitudeConstraint;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity(repositoryClass: OutOfBoundsLatitudeRepository::class),
]
class OutOfRangeLatitude extends AbstractOutOfRange
{
    #[LatitudeConstraint]
    protected int|float $sensorReading;
}
