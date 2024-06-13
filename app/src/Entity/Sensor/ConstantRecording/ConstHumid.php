<?php

namespace App\Entity\Sensor\ConstantRecording;

use App\CustomValidators\Sensor\SensorDataValidators\HumidityConstraint;
use App\Repository\Sensor\ConstRecord\ORM\ConstantlyRecordHumidRepository;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity(repositoryClass: ConstantlyRecordHumidRepository::class),
]
class ConstHumid extends AbstractConstRecord
{
    #[HumidityConstraint]
    protected float $sensorReading;
}
