<?php

namespace App\Sensors\Entity;

use App\Common\Entity\Operator;
use App\Sensors\Repository\SensorTriggerRepository;
use App\User\Entity\User;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity(repositoryClass: SensorTriggerRepository::class),
    ORM\Table(name: "sensortrigger"),
    ORM\Index(columns: ["sensorID"], name: "IDX_1F9B6F4F8D93D649"),
    ORM\Index(columns: ["sensorToTriggerID"], name: "IDX_1F9B6F4F8D93D6492"),
    ORM\Index(columns: ["operatorID"], name: "IDX_1F9B6F4F8D93D6493"),
]
class SensorTrigger
{
    #[
        ORM\Column(name: "sensorTriggerID", type: "integer", nullable: false),
        ORM\Id,
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    private int $sensorTriggerID;

    #[
        ORM\ManyToOne(targetEntity: Sensor::class),
        ORM\JoinColumn(name: "sensorID", referencedColumnName: "sensorID"),
    ]
    private Sensor $sensor;

    #[
        ORM\ManyToOne(targetEntity: Sensor::class),
        ORM\JoinColumn(name: "sensorToTriggerID", referencedColumnName: "sensorID"),
    ]
    private Sensor $sensorToTrigger;

    #[
        ORM\Column(name: "valueThatTriggers", type: "string", length: 255, nullable: false),
        Assert\NotBlank(message: "Value that triggers cannot be blank"),
        Assert\Length(
            min: 1,
            max: 255,
            minMessage: "Value that triggers must be at least {{ limit }} characters long",
            maxMessage: "Value that triggers cannot be longer than {{ limit }} characters"
        ),
    ]
    private string $valueThatTriggers;

    #[
        ORM\ManyToOne(targetEntity: Operator::class),
        ORM\JoinColumn(name: "operatorID", referencedColumnName: "operatorID"),
    ]
    private Operator $operator;

    #[
        ORM\ManyToOne(targetEntity: User::class),
        ORM\JoinColumn(name: "createdBy", referencedColumnName: "userID"),
    ]
    private UserInterface $createdBy;

    #[
        ORM\Column(name: "createdAt", type: "datetime", nullable: false),
        Assert\NotBlank(message: "Created at cannot be blank"),
        Assert\DateTime(message: "Created at is not valid")
    ]
    private DateTimeInterface $createdAt;

    #[
        ORM\Column(name: "updatedAt", type: "datetime", nullable: false),
        Assert\NotBlank(message: "Updated at cannot be blank"),
        Assert\DateTime(message: "Updated at is not valid")
    ]
    private DateTimeInterface $updatedAt;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable('now');
        $this->updatedAt = new DateTimeImmutable('now');
    }

    public function getSensorTriggerID(): int
    {
        return $this->sensorTriggerID;
    }

    public function getSensor(): Sensor
    {
        return $this->sensor;
    }

    public function setSensor(Sensor $sensor): void
    {
        $this->sensor = $sensor;
    }

    public function getSensorToTrigger(): Sensor
    {
        return $this->sensorToTrigger;
    }

    public function setSensorToTrigger(Sensor $sensorToTrigger): void
    {
        $this->sensorToTrigger = $sensorToTrigger;
    }

    public function getValueThatTriggers(): string
    {
        return $this->valueThatTriggers;
    }

    public function setValueThatTriggers(string $valueThatTriggers): void
    {
        $this->valueThatTriggers = $valueThatTriggers;
    }

    public function getOperator(): Operator
    {
        return $this->operator;
    }

    public function setOperator(Operator $operator): void
    {
        $this->operator = $operator;
    }

    public function getCreatedBy(): UserInterface
    {
        return $this->createdBy;
    }

    public function setCreatedBy(UserInterface $createdBy): void
    {
        $this->createdBy = $createdBy;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
