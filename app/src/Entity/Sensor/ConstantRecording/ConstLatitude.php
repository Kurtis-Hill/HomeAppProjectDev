<?php

namespace App\Entity\Sensor\ConstantRecording;

use App\Repository\Sensor\ConstRecord\ORM\ConstantlyRecordLatitudeRepository;
use App\Services\CustomValidators\Sensor\SensorDataValidators\LatitudeConstraint;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity(repositoryClass: ConstantlyRecordLatitudeRepository::class),
]
class ConstLatitude extends AbstractConstRecord
{
    #[LatitudeConstraint]
    protected float $sensorReading;
}
