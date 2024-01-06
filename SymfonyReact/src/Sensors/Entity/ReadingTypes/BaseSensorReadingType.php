<?php

namespace App\Sensors\Entity\ReadingTypes;

use App\Sensors\Entity\Sensor;
use App\Sensors\Repository\ReadingType\ORM\BaseSensorReadingTypeRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[Entity(repositoryClass: BaseSensorReadingTypeRepository::class)]
#[ORM\Table(name: 'basereadingtype')]
class BaseSensorReadingType
{
    #[
        ORM\Column(name: 'baseReadingTypeID', type: "integer", nullable: false),
        ORM\Id,
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    private int $readingTypeID;

    #[
        ORM\ManyToOne(targetEntity: Sensor::class),
        ORM\JoinColumn(name: "sensorID", referencedColumnName: "sensorID"),
    ]
    private Sensor $sensor;

    #[ORM\Column(name: 'constRecord', type: "boolean", nullable: false, options: ["default" => "0"])]
    #[Assert\Type("bool")]
    private bool $constRecord = false;

    #[
        ORM\Column(name: 'updatedAt', type: "datetime", nullable: false),
        Assert\NotBlank(message: 'date time name should not be blank')
    ]
    private DateTimeInterface $updatedAt;

    #[ORM\Column(
        name: 'createdAt',
        type: "datetime",
        nullable: false,
    )]
    #[Assert\NotBlank(message: 'createdAt time name should not be blank')]
    protected DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->updatedAt = new DateTimeImmutable('now');
    }

    public function getReadingTypeID(): int
    {
        return $this->readingTypeID;
    }

    public function getSensor(): Sensor
    {
        return $this->sensor;
    }

    public function setSensor(Sensor $sensor): void
    {
        $this->sensor = $sensor;
    }

    public function getConstRecord(): bool
    {
        return $this->constRecord;
    }

    public function setConstRecord(bool $constRecord): void
    {
        $this->constRecord = $constRecord;
    }

    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(): void
    {
        $this->updatedAt = new DateTimeImmutable('now');
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTimeInterface $createdAt = null): void
    {
        if ($createdAt === null) {
            $createdAt = new DateTimeImmutable('now');
        }
        $this->createdAt = $createdAt;
    }
}
