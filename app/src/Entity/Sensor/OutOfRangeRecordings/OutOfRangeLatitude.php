<?php

namespace App\Entity\Sensor\OutOfRangeRecordings;

use App\CustomValidators\Sensor\SensorDataValidators\LatitudeConstraint;
use App\Repository\Sensor\OutOfBounds\ORM\OutOfBoundsLatitudeRepository;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity(repositoryClass: OutOfBoundsLatitudeRepository::class),
]
class OutOfRangeLatitude extends AbstractOutOfRange
{
    #[LatitudeConstraint]
    protected int|float $sensorReading;
}
