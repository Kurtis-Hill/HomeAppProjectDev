<?php

namespace App\Sensors\Entity\ConstantRecording;

use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\LatitudeConstraint;
use App\Sensors\Repository\ConstRecord\ORM\ConstantlyRecordLatitudeRepository;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity(repositoryClass: ConstantlyRecordLatitudeRepository::class),
]
class ConstLatitude extends AbstractConstRecord
{
    #[LatitudeConstraint]
    protected float $sensorReading;
}
