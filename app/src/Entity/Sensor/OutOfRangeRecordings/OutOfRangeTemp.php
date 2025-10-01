<?php

namespace App\Entity\Sensor\OutOfRangeRecordings;

use App\Entity\Sensor\SensorTypes\Bmp;
use App\Entity\Sensor\SensorTypes\Dallas;
use App\Entity\Sensor\SensorTypes\Dht;
use App\Entity\Sensor\SensorTypes\Sht;
use App\Repository\Sensor\OutOfBounds\ORM\OutOfBoundsTempRepository;
use App\CustomValidators\Sensor\SensorDataValidators\BMP280TemperatureConstraint;
use App\CustomValidators\Sensor\SensorDataValidators\DallasTemperatureConstraint;
use App\CustomValidators\Sensor\SensorDataValidators\DHTTemperatureConstraint;
use App\CustomValidators\Sensor\SensorDataValidators\SHTTemperatureConstraint;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity(repositoryClass: OutOfBoundsTempRepository::class),
]
class OutOfRangeTemp extends AbstractOutOfRange
{
    #[ORM\Column(name: "sensorReading", type: "float", precision: 10, scale: 0, nullable: false),]
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
        )
    ]
    protected float $sensorReading;
}
