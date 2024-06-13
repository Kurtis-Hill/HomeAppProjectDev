<?php

namespace App\Entity\Sensor\OutOfRangeRecordings;

use App\CustomValidators\Sensor\SensorDataValidators\HumidityConstraint;
use App\Repository\Sensor\OutOfBounds\ORM\OutOfBoundsHumidityRepository;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity(repositoryClass: OutOfBoundsHumidityRepository::class),
]
class OutOfRangeHumid extends AbstractOutOfRange
{
    #[HumidityConstraint]
    protected float $sensorReading;
}
