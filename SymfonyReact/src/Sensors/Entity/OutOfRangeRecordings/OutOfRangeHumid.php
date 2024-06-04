<?php

namespace App\Sensors\Entity\OutOfRangeRecordings;

use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\HumidityConstraint;
use App\Sensors\Repository\OutOfBounds\ORM\OutOfBoundsHumidityRepository;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity(repositoryClass: OutOfBoundsHumidityRepository::class),
]
class OutOfRangeHumid extends AbstractOutOfRange
{
    #[HumidityConstraint]
    protected float $sensorReading;
}
