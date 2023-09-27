<?php

namespace App\Sensors\Entity\SensorTypes;

use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Motion;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Interfaces\MotionSensorReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Repository\SensorType\ORM\GenericMotionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity(repositoryClass: GenericMotionRepository::class),
    ORM\Table(name: "genericmotion"),
    ORM\UniqueConstraint(name: "UNIQ_GENERIC_MOTION", columns: ["sensorID"]),
]
class GenericMotion implements SensorTypeInterface, MotionSensorReadingTypeInterface, BoolSensorTypeInterface
{
    public const NAME = 'GenericMotion';

    public const ALIAS = 'genericMotion';

    private const ALLOWED_READING_TYPES = [
        Motion::READING_TYPE,
    ];

    #[
        ORM\Column(name: "genericMotionID", type: "integer", nullable: false),
        ORM\Id,
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    private int $genericMotionID;

    #[
        ORM\ManyToOne(targetEntity: Motion::class),
        ORM\JoinColumn(name: "motionID", referencedColumnName: "boolID"),
    ]
    private Motion $motion;

    #[
        ORM\ManyToOne(targetEntity: Sensor::class),
        ORM\JoinColumn(name: "sensorID", referencedColumnName: "sensorID"),
    ]
    private Sensor $sensor;

    public function getGenericMotionID(): int
    {
        return $this->genericMotionID;
    }

    public function getMotion(): Motion
    {
        return $this->motion;
    }

    public function setMotion(Motion $motion): void
    {
        $this->motion = $motion;
    }

    public function getSensor(): Sensor
    {
        return $this->sensor;
    }

    public function setSensor(Sensor $sensor): void
    {
        $this->sensor = $sensor;
    }

    public function getReadingTypeName(): string
    {
        return self::NAME;
    }

    public static function getReadingTypeAlias(): string
    {
        return self::ALIAS;
    }

    public static function getAllowedReadingTypes(): array
    {
        return self::ALLOWED_READING_TYPES;
    }

    public function getSensorTypeID(): int
    {
        return $this->genericMotionID;
    }

    public function getReadingTypes(): Collection
    {
        return new ArrayCollection([$this->motion]);
    }
}
