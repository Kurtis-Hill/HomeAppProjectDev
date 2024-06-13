<?php

namespace App\Entity\Sensor\ConstantRecording;

use App\CustomValidators\Sensor\SensorDataValidators\LatitudeConstraint;
use App\Repository\Sensor\ConstRecord\ORM\ConstantlyRecordLatitudeRepository;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity(repositoryClass: ConstantlyRecordLatitudeRepository::class),
]
class ConstLatitude extends AbstractConstRecord
{
    #[LatitudeConstraint]
    protected float $sensorReading;
}
