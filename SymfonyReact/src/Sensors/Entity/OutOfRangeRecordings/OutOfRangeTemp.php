<?php

namespace App\Sensors\Entity\OutOfRangeRecordings;

use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\StandardReadingSensorInterface;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Temperature;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\Sht;
use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\BMP280TemperatureConstraint;
use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\DallasTemperatureConstraint;
use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\DHTTemperatureConstraint;
use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\SHTTemperatureConstraint;
use App\Sensors\Repository\OutOfBounds\ORM\OutOfBoundsTempRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity(repositoryClass: OutOfBoundsTempRepository::class),
]
class OutOfRangeTemp extends AbstractOutOfRange implements OutOfBoundsEntityInterface
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
