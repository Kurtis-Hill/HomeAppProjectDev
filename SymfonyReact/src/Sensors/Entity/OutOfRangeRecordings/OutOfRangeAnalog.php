<?php

namespace App\Sensors\Entity\OutOfRangeRecordings;

use App\Sensors\Entity\SensorTypes\LDR;
use App\Sensors\Entity\SensorTypes\Soil;
use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\LDRConstraint;
use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\SoilConstraint;
use App\Sensors\Repository\OutOfBounds\ORM\OutOfBoundsAnalogRepository;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity(repositoryClass: OutOfBoundsAnalogRepository::class),
]
class OutOfRangeAnalog extends AbstractOutOfRange implements OutOfBoundsEntityInterface
{
    #[
        SoilConstraint(groups: [Soil::NAME]),
        LDRConstraint(groups: [LDR::NAME]),
    ]
    protected float $sensorReading;
}
