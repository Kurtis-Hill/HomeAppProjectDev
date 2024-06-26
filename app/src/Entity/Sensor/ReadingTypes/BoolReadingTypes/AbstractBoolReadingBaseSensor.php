<?php

namespace App\Entity\Sensor\ReadingTypes\BoolReadingTypes;

use App\Entity\Sensor\ReadingTypes\BaseReadingTypeInterface;
use App\Entity\Sensor\ReadingTypes\BaseSensorReadingType;
use App\Entity\Sensor\Sensor;
use App\Entity\Sensor\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Repository\Sensor\SensorReadingType\ORM\BoolReadingBaseSensorRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\InheritanceType;

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
abstract class AbstractBoolReadingBaseSensor implements BaseReadingTypeInterface, BoolReadingSensorInterface, AllSensorReadingTypeInterface
{
    #[
        ORM\Column(name: "readingTypeID", type: "integer", nullable: false),
        ORM\Id,
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    protected int $boolID;

    #[
        ORM\OneToOne(targetEntity: BaseSensorReadingType::class),
        ORM\JoinColumn(name: "baseReadingTypeID", referencedColumnName: "baseReadingTypeID"),
    ]
    private BaseSensorReadingType $baseReadingType;

    #[ORM\Column(name: "currentReading", type: "boolean", nullable: false)]
    protected bool $currentReading;

    #[ORM\Column(name: "requestedReading", type: "boolean", nullable: false)]
    protected bool $requestedReading;

    #[ORM\Column(name: "expectedReading", type: "boolean", nullable: true)]
    private ?bool $expectedReading;

    public function getReadingTypeID(): int
    {
        return $this->boolID;
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

    public function getSensor(): Sensor
    {
        return $this->getBaseReadingType()->getSensor();
    }

    public function setSensor(Sensor $sensor): void
    {
        $this->getBaseReadingType()->setSensor($sensor);
    }

    public function getConstRecord(): bool
    {
        return $this->getBaseReadingType()->getConstRecord();
    }

    public function setConstRecord(bool $constRecord): void
    {
        $this->getBaseReadingType()->setConstRecord($constRecord);
    }

    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->getBaseReadingType()->getUpdatedAt();
    }

    public function setUpdatedAt(): void
    {
        $this->getBaseReadingType()->setUpdatedAt();
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->getBaseReadingType()->getCreatedAt();
    }

    public function setCreatedAt(?DateTimeInterface $createdAt = null): void
    {
        if ($createdAt === null) {
            $createdAt = new DateTimeImmutable('now');
        }
        $this->getBaseReadingType()->setCreatedAt($createdAt);
    }

    public function getSensorID(): int
    {
        return $this->getBaseReadingType()->getSensor()->getSensorID();
    }
}
