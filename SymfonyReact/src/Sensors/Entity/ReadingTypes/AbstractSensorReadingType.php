<?php

namespace App\Sensors\Entity\ReadingTypes;

use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\AbstractBoolReadingSensor;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\AbstractStandardReadingType;
use App\Sensors\Entity\Sensor;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\InheritanceType;
use Doctrine\ORM\Mapping as ORM;

#[Entity]
#[InheritanceType('SINGLE_TABLE')]
#[ORM\Table(name: 'sensor_reading_type')]
#[DiscriminatorColumn(name: 'baseReadingTypes', type: 'string')]
#[DiscriminatorMap([
    self::BOOL_READING_TYPE => AbstractBoolReadingSensor::class,
    self::STANDARD_READING_TYPE => AbstractStandardReadingType::class
])]
abstract class AbstractSensorReadingType
{
    public const BOOL_READING_TYPE = 'bool';

    public const STANDARD_READING_TYPE = 'standard';

    #[
        ORM\Column(name: 'readingTypeID', type: "integer", nullable: false),
        ORM\Id,
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    protected int $readingTypeID;

    #[
        ORM\ManyToOne(targetEntity: Sensor::class),
        ORM\JoinColumn(name: "sensorID", referencedColumnName: "sensorID"),
    ]
    protected Sensor $sensor;

    #[ORM\Column(name: "createdAt", type: "datetime", nullable: false)]
    protected DateTimeInterface $createdAt;

    #[ORM\Column(name: "updatedAt", type: "datetime", nullable: false)]
    protected DateTimeInterface $updatedAt;

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(): void
    {
        $this->updatedAt = new DateTimeImmutable('now');
    }
}
