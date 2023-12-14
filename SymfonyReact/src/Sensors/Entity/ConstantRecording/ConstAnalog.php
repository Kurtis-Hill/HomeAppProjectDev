<?php

namespace App\Sensors\Entity\ConstantRecording;

use App\Sensors\Entity\SensorTypes\LDR;
use App\Sensors\Entity\SensorTypes\Soil;
use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\LDRConstraint;
use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\SoilConstraint;
use App\Sensors\Repository\ConstRecord\ORM\ConstantlyRecordAnalogRepository;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity(repositoryClass: ConstantlyRecordAnalogRepository::class),
]
class ConstAnalog extends AbstractConstRecord implements ConstantlyRecordEntityInterface
{
    #[
        SoilConstraint(groups: [Soil::NAME]),
        LDRConstraint(groups: [LDR::NAME]),
    ]
    protected float $sensorReading;
}
