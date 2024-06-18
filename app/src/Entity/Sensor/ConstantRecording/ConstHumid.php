<?php

namespace App\Entity\Sensor\ConstantRecording;

use App\Repository\Sensor\ConstRecord\ORM\ConstantlyRecordHumidRepository;
use App\Services\CustomValidators\Sensor\SensorDataValidators\HumidityConstraint;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity(repositoryClass: ConstantlyRecordHumidRepository::class),
]
class ConstHumid extends AbstractConstRecord
{
    #[HumidityConstraint]
    protected float $sensorReading;
}
