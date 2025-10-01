<?php

namespace App\Entity\Sensor\ConstantRecording;

use App\Entity\Sensor\SensorTypes\LDR;
use App\Entity\Sensor\SensorTypes\Soil;
use App\Repository\Sensor\ConstRecord\ORM\ConstantlyRecordAnalogRepository;
use App\CustomValidators\Sensor\SensorDataValidators\LDRConstraint;
use App\CustomValidators\Sensor\SensorDataValidators\SoilConstraint;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity(repositoryClass: ConstantlyRecordAnalogRepository::class),
]
class ConstAnalog extends AbstractConstRecord
{
    #[
        SoilConstraint(groups: [Soil::NAME]),
        LDRConstraint(groups: [LDR::NAME]),
    ]
    protected float $sensorReading;
}
