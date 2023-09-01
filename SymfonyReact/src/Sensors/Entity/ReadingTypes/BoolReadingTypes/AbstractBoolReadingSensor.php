<?php

namespace App\Sensors\Entity\ReadingTypes\BoolReadingTypes;

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
#[ORM\Table(name: 'boolsensor')]
#[DiscriminatorColumn(name: 'boolReadingType', type: 'string')]
#[DiscriminatorMap(['relay' => Relay::class, 'motion' => Motion::class])]
abstract class AbstractBoolReadingSensor implements BoolReadingSensorInterface
{
    #[
        ORM\Column(name: "boolID", type: "integer", nullable: false),
        ORM\Id,
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    protected int $boolID;

    #[
        ORM\ManyToOne(targetEntity: Sensor::class),
        ORM\JoinColumn(name: "sensorID", referencedColumnName: "sensorID"),
    ]
    protected Sensor $sensor;

    #[ORM\Column(name: "currentReading", type: "boolean", nullable: false)]
    protected bool $currentReading;

    #[ORM\Column(name: "requestedReading", type: "boolean", nullable: false)]
    protected bool $requestedReading;

    #[ORM\Column(name: "expectedReading", type: "boolean", nullable: true)]
    protected ?bool $expectedReading;

//    #[ORM\Column(name: "boolReadingType", type: "string", nullable: false)]
//    protected string $boolReadingType;

    #[ORM\Column(name: "createdAt", type: "datetime", nullable: false)]
    protected DateTimeInterface $createdAt;

    #[ORM\Column(name: "updatedAt", type: "datetime", nullable: false)]
    protected DateTimeInterface $updatedAt;

    protected bool $constRecord = false;

    public function getBoolID(): int
    {
        return $this->boolID;
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

    public function getConstRecord(): bool
    {
        return $this->constRecord;
    }

    public function setConstRecord(bool $constRecord): void
    {
        $this->constRecord = $constRecord;
    }
}
