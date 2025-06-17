<?php

namespace App\Entity\Sensor\OutOfRangeRecordings;

use App\Entity\Sensor\SensorTypes\LDR;
use App\Entity\Sensor\SensorTypes\Soil;
use App\Repository\Sensor\OutOfBounds\ORM\OutOfBoundsAnalogRepository;
use App\CustomValidators\Sensor\SensorDataValidators\LDRConstraint;
use App\CustomValidators\Sensor\SensorDataValidators\SoilConstraint;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity(repositoryClass: OutOfBoundsAnalogRepository::class),
]
class OutOfRangeAnalog extends AbstractOutOfRange
{
    #[
        SoilConstraint(groups: [Soil::NAME]),
        LDRConstraint(groups: [LDR::NAME]),
    ]
    protected float $sensorReading;
}
