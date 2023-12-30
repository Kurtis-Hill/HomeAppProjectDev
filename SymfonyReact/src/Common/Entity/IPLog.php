<?php
declare(strict_types=1);

namespace App\Common\Entity;

use App\Common\Repository\IPLogRepository;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity(repositoryClass: IPLogRepository::class),
    ORM\Table(name: "iplog"),
    ORM\UniqueConstraint(name: "ipAddress", columns: ["ipAddress"]),
]
class IPLog
{
    #[
        ORM\Column(name: "ipLogID", type: "integer", nullable: false),
        ORM\Id,
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    private int $ipLogID;

    #[
        ORM\Column(name: "ipAddress", type: "string", length: 13, nullable: false),
        Assert\NotBlank(message: "IP address cannot be blank"),
        Assert\Ip(message: "IP address is not valid"),
        Assert\Length(
            min: 7,
            max: 13,
            minMessage: "IP address must be at least {{ limit }} characters long",
            maxMessage: "IP address cannot be longer than {{ limit }} characters"
        ),
    ]
    private string $ipAddress;

    #[
        ORM\Column(name: "createdAt", type: "datetime", nullable: false),
        Assert\NotBlank(message: "Created at cannot be blank"),
        Assert\DateTime(message: "Created at is not valid")
    ]
    private DateTimeInterface $createdAt;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable('now');
    }

    public function getIpLogID(): int
    {
        return $this->ipLogID;
    }

    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }

    public function setIpAddress(string $ipAddress): void
    {
        $this->ipAddress = $ipAddress;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }
}
