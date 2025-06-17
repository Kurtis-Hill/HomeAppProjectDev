<?php

namespace App\Entity\Sensor\ConstantRecording;

use App\Entity\Sensor\SensorTypes\Bmp;
use App\Entity\Sensor\SensorTypes\Dallas;
use App\Entity\Sensor\SensorTypes\Dht;
use App\Entity\Sensor\SensorTypes\Sht;
use App\Repository\Sensor\ConstRecord\ORM\ConstantlyRecordTempRepository;
use App\CustomValidators\Sensor\SensorDataValidators\BMP280TemperatureConstraint;
use App\CustomValidators\Sensor\SensorDataValidators\DallasTemperatureConstraint;
use App\CustomValidators\Sensor\SensorDataValidators\DHTTemperatureConstraint;
use App\CustomValidators\Sensor\SensorDataValidators\SHTTemperatureConstraint;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity(repositoryClass: ConstantlyRecordTempRepository::class),
]
class ConstTemp extends AbstractConstRecord
{
    #[
        DallasTemperatureConstraint(
            groups: [Dallas::NAME]
        ),
        DHTTemperatureConstraint(
            groups: [Dht::NAME]
        ),
        BMP280TemperatureConstraint(
            groups:[Bmp::NAME]
        ),
        SHTTemperatureConstraint(
            groups: [Sht::NAME]
        ),
    ]
    protected float $sensorReading;
}
