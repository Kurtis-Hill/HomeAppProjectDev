<?php

namespace App\Sensors\Entity\ConstantRecording;

use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\HumidityConstraint;
use App\Sensors\Repository\ConstRecord\ORM\ConstantlyRecordHumidRepository;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity(repositoryClass: ConstantlyRecordHumidRepository::class),
]
class ConstHumid extends AbstractConstRecord
{
    #[HumidityConstraint]
    protected float $sensorReading;
}
