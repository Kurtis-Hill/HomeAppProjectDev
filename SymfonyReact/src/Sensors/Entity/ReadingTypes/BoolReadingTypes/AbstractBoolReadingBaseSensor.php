<?php

namespace App\Sensors\Entity\ReadingTypes\BoolReadingTypes;

use App\Sensors\Entity\ReadingTypes\BaseReadingTypeInterface;
use App\Sensors\Entity\ReadingTypes\BaseSensorReadingType;
use App\Sensors\Entity\Sensor;
use App\Sensors\Repository\SensorReadingType\ORM\BoolReadingBaseSensorRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\InheritanceType;
use Doctrine\ORM\Mapping as ORM;

#[
    Entity(repositoryClass: BoolReadingBaseSensorRepository::class),
    InheritanceType('SINGLE_TABLE'),
    ORM\Table(name: 'boolreadingtype'),
    ORM\Index(columns: ["currentReading"], name: "currentReading"),
    ORM\Index(columns: ["constRecord"], name: "constRecord"),
    ORM\Index(columns: ["updatedAt"], name: "updatedAt"),
    ORM\Index(columns: ["standardReadingType"], name: "standardreadingtypeIndex"),
    ORM\Index(columns: ["sensorID"], name: "sensorID"),
    ORM\Index(columns: ["createdAt"], name: "createdAt"),
    DiscriminatorColumn(name: 'boolReadingType', type: 'string'),
    DiscriminatorMap(
        [
            'relay' => Relay::class,
            'motion' => Motion::class
        ]
    )
]
abstract class AbstractBoolReadingBaseSensor implements BaseReadingTypeInterface, BoolReadingSensorInterface
{
    #[
        ORM\Column(name: "readingTypeID", type: "integer", nullable: false),
        ORM\Id,
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    private int $boolID;

    #[
        ORM\OneToOne(targetEntity: BaseSensorReadingType::class),
        ORM\JoinColumn(name: "baseReadingTypeID", referencedColumnName: "baseReadingTypeID"),
    ]
    private BaseSensorReadingType $baseReadingType;

    #[
        ORM\ManyToOne(targetEntity: Sensor::class),
        ORM\JoinColumn(name: "sensorID", referencedColumnName: "sensorID"),
    ]
    private Sensor $sensor;

    #[ORM\Column(name: "currentReading", type: "boolean", nullable: false)]
    private bool $currentReading;

    #[ORM\Column(name: "requestedReading", type: "boolean", nullable: false)]
    private bool $requestedReading;

    #[ORM\Column(name: "expectedReading", type: "boolean", nullable: true)]
    private ?bool $expectedReading;

//    #[ORM\Column(name: "boolReadingType", type: "string", nullable: false)]
//    protected string $boolReadingType;

//    #[ORM\Column(name: "createdAt", type: "datetime", nullable: false)]
//    protected DateTimeInterface $createdAt;
//
    #[ORM\Column(name: "updatedAt", type: "datetime", nullable: false)]
    protected DateTimeInterface $updatedAt;

    #[ORM\Column(name: 'constRecord', type: "boolean", nullable: false)]
    protected bool $constRecord = false;

    public function __construct()
    {
        $this->updatedAt = new DateTimeImmutable('now');
        $this->setUpdatedAt();
    }

    public function getBoolID(): int
    {
        return $this->boolID;
    }

    public function getBaseReadingType(): BaseSensorReadingType
    {
        return $this->baseReadingType;
    }

    public function setBaseReadingType(BaseSensorReadingType $baseReadingType): void
    {
        $this->baseReadingType = $baseReadingType;
    }

    public function getSensor(): Sensor
    {
        return $this->sensor;
    }

    public function setSensor(Sensor $sensor): void
    {
        $this->sensor = $sensor;
    }

    public function getCurrentReading(): bool
    {
        return $this->currentReading;
    }

    public function setCurrentReading(int|float|string|bool $currentReading): void
    {
        $this->currentReading = $currentReading;
    }

    public function getRequestedReading(): bool
    {
        return $this->requestedReading;
    }

    public function setRequestedReading(bool $requestedReading): void
    {
        $this->requestedReading = $requestedReading;
    }

    public function getExpectedReading(): ?bool
    {
        return $this->expectedReading;
    }

    public function setExpectedReading(?bool $expectedReading): void
    {
        $this->expectedReading = $expectedReading;
    }

//    public function getReadingType(): string
//    {
//        return $this->boolReadingType;
//    }

//    public function setBoolReadingType(string $boolReadingType): void
//    {
//        $this->boolReadingType = $boolReadingType;
//    }

//    public function getCreatedAt(): DateTimeInterface
//    {
//        return $this->createdAt;
//    }
//
//    public function setCreatedAt(DateTimeInterface $createdAt): void
//    {
//        $this->createdAt = $createdAt;
//    }
//
    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(): void
    {
        $this->updatedAt = new DateTimeImmutable('now');
    }

    public function getConstRecord(): bool
    {
        return $this->constRecord;
    }

    public function setConstRecord(bool $constRecord): void
    {
        $this->constRecord = $constRecord;
    }

    public function getSensorID(): int
    {
        return $this->getSensor()->getSensorID();
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->getSensor()->getCreatedAt();
    }

    public function setCreatedAt(DateTimeInterface $createdAt): void
    {
        $this->getSensor()->setCreatedAt($createdAt);
    }
}
