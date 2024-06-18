<?php

namespace App\Entity\Sensor\OutOfRangeRecordings;

use App\Repository\Sensor\OutOfBounds\ORM\OutOfBoundsHumidityRepository;
use App\Services\CustomValidators\Sensor\SensorDataValidators\HumidityConstraint;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity(repositoryClass: OutOfBoundsHumidityRepository::class),
]
class OutOfRangeHumid extends AbstractOutOfRange
{
    #[HumidityConstraint]
    protected float $sensorReading;
}
