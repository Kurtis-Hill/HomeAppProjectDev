<?php

namespace App\Common\Entity;

use App\Common\Repository\TriggerTypeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity(repositoryClass: TriggerTypeRepository::class),
    ORM\Table(name: "triggerType"),
    ORM\UniqueConstraint(name: "triggerTypeName", columns: ["triggerTypeName"]),
]
class TriggerType
{
    private const EMAIL_TRIGGER = 'Email';

    private const RELAY_UP_TRIGGER = 'Relay Up';

    private const RELAY_DOWN_TRIGGER = 'Relay Down';

    private const TRIGGER_TYPES = [
        self::EMAIL_TRIGGER,
        self::RELAY_UP_TRIGGER,
        self::RELAY_DOWN_TRIGGER,
    ];

    #[
        ORM\Column(name: "triggerTypeID", type: "integer", nullable: false),
        ORM\Id,
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    private int $triggerTypeID;

    #[
        ORM\Column(name: "triggerTypeName", type: "string", length: 255, nullable: false),
        Assert\NotBlank(message: "Trigger type name cannot be blank"),
    ]
    private string $triggerTypeName;

    #[
        ORM\Column(name: "triggerTypeDescription", type: "string", length: 255, nullable: false),
        Assert\NotBlank(message: "Trigger type description cannot be blank"),
    ]
    private string $triggerTypeDescription;

    public function getTriggerTypeID(): int
    {
        return $this->triggerTypeID;
    }

    public function getTriggerTypeName(): string
    {
        return $this->triggerTypeName;
    }

    public function setTriggerTypeName(string $triggerTypeName): void
    {
        $this->triggerTypeName = $triggerTypeName;
    }

    public function getTriggerTypeDescription(): string
    {
        return $this->triggerTypeDescription;
    }

    public function setTriggerTypeDescription(string $triggerTypeDescription): void
    {
        $this->triggerTypeDescription = $triggerTypeDescription;
    }
}
