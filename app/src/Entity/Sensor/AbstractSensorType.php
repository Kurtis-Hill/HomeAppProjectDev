<?php

namespace App\Entity\Sensor;

use App\CustomValidators\NoSpecialCharactersNameConstraint;
use App\Entity\Sensor\SensorTypes\Bmp;
use App\Entity\Sensor\SensorTypes\Dallas;
use App\Entity\Sensor\SensorTypes\Dht;
use App\Entity\Sensor\SensorTypes\GenericMotion;
use App\Entity\Sensor\SensorTypes\GenericRelay;
use App\Entity\Sensor\SensorTypes\Interfaces\SensorTypeInterface;
use App\Entity\Sensor\SensorTypes\LDR;
use App\Entity\Sensor\SensorTypes\Sht;
use App\Entity\Sensor\SensorTypes\Soil;
use App\Repository\Sensor\Sensors\ORM\SensorTypeRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\ORM\Mapping\InheritanceType;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity(repositoryClass: SensorTypeRepository::class),
    ORM\Table(name: "sensortype"),
    ORM\UniqueConstraint(name: "sensorType", columns: ["sensorType"]),
    InheritanceType('SINGLE_TABLE'),
    DiscriminatorColumn(name: 'sensorType', type: 'string'),
    DiscriminatorMap(
        [
            Dht::NAME => Dht::class,
            Bmp::NAME => Bmp::class,
            Soil::NAME => Soil::class,
            Dallas::NAME => Dallas::class,
            GenericMotion::NAME => GenericMotion::class,
            GenericRelay::NAME => GenericRelay::class,
            LDR::NAME => LDR::class,
            Sht::NAME => Sht::class,
        ]
    )
]
abstract class AbstractSensorType implements SensorTypeInterface
{
    public const ALIAS = 'sensortype';

    public const STANDARD_READING_SENSOR_TYPE = 'standardReading';

    public const BOOL_READING_SENSOR_TYPE = 'boolReading';

    public const ALL_SENSOR_TYPES = [
        Bmp::NAME,
        Soil::NAME,
        Dallas::NAME,
        Dht::NAME,
        GenericMotion::NAME,
        GenericRelay::NAME,
        LDR::NAME,
        Sht::NAME,
    ];

    private const SENSOR_TYPE_DESCRIPTION_MIN_LENGTH = 5;

    private const SENSOR_TYPE_DESCRIPTION_MAX_LENGTH = 50;

    #[
        ORM\Column(name: "sensorTypeID", type: "integer", nullable: false),
        ORM\Id,
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    private int $sensorTypeID;

//    #[ORM\Column(name: "sensorType", type: "string", length: 20, nullable: false)]
//    #[NoSpecialCharactersNameConstraint]
//    private SensorTypeInterface $sensorType;

    #[ORM\Column(name: "description", type: "string", length: 50, nullable: false)]
    #[
        NoSpecialCharactersNameConstraint,
        Assert\Length(
            min: self::SENSOR_TYPE_DESCRIPTION_MIN_LENGTH,
            max: self::SENSOR_TYPE_DESCRIPTION_MAX_LENGTH,
            minMessage: "Sensor name must be at least {{ limit }} characters long",
            maxMessage: "Sensor name cannot be longer than {{ limit }} characters"
        ),
        Assert\NotBlank,
    ]
    private string $description;

    public function getSensorTypeID(): int
    {
        return $this->sensorTypeID;
    }

    public function setSensorTypeID(int $sensorTypeID): void
    {
        $this->sensorTypeID = $sensorTypeID;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
}
