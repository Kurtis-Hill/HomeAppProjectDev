<?php

namespace App\Devices\Entity;

use App\Common\CustomValidators\NoSpecialCharactersNameConstraint;
use App\Devices\Repository\ORM\DeviceRepository;
use App\User\Entity\Group;
use App\User\Entity\Room;
use App\User\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[
    ORM\Entity(repositoryClass: DeviceRepository::class),
    ORM\Table(name: "devices"),
    ORM\Index(columns: ["createdBy"], name: "createdBy"),
    ORM\Index(columns: ["groupID"], name: "groupID"),
    ORM\Index(columns: ["roomID"], name: "roomID"),
    ORM\UniqueConstraint(name: "device_room_un", columns: ["deviceName", "roomID"]),
]
class Devices implements UserInterface, PasswordAuthenticatedUserInterface
{
    private const DEVICE_NAME_MIN_LENGTH = 2;

    private const DEVICE_NAME_MAX_LENGTH = 50;

    public const ROLE = 'ROLE_DEVICE';

    public const ALIAS = 'device';

    public const USER_TYPE = 'user';

    #[
        ORM\Id,
        ORM\GeneratedValue(strategy: "IDENTITY"),
        ORM\Column(name: 'deviceID', type: "integer", nullable: false)
    ]
    private int $deviceID;

    #[ORM\Column(name: "deviceName", type: "string", length: 20, nullable: false)]
    #[
        NoSpecialCharactersNameConstraint,
        Assert\NotBlank(
            message: 'Device name should not be blank'
        ),
        Assert\Length(
            min: self::DEVICE_NAME_MIN_LENGTH,
            max: self::DEVICE_NAME_MAX_LENGTH,
            minMessage: "Device name must be at least {{ limit }} characters long",
            maxMessage: "Device name cannot be longer than {{ limit }} characters"
        )
    ]
    private ?string $deviceName;

    #[ORM\Column(name: "password", type: "text", length: 100, nullable: false)]
    #[
        Assert\NotBlank(message: 'Password should not be blank', groups: ['final-check']),
        Assert\Length(
            min: 5,
            max: 100,
            minMessage: "Device password must be at least {{ limit }} characters long",
            maxMessage: "Device password cannot be longer than {{ limit }} characters"
        )
    ]
    private string $password;

    #[
        ORM\ManyToOne(targetEntity: User::class),
        ORM\JoinColumn(name: "createdBy", referencedColumnName: "userID"),
    ]
    #[Assert\NotBlank(message: 'UserExceptions object should not be blank')]
    private User $createdBy;

    #[
        ORM\ManyToOne(targetEntity: Group::class),
        ORM\JoinColumn(name: "groupID", referencedColumnName: "groupID"),
    ]
    #[Assert\NotBlank(message: 'Group name should not be blank')]
    private Group $groupID;

    #[
        ORM\ManyToOne(targetEntity: Room::class),
        ORM\JoinColumn(name: "roomID", referencedColumnName: "roomID"),
    ]
    #[Assert\NotBlank(message: 'Room should not be blank')]
    private Room $roomID;

    #[ORM\Column(name: "roles", type: "json", nullable: false)]
    private array $roles;

    #[ORM\Column(name: "ipAddress", type: "string", length: 13, nullable: true, options: ["default" => "NULL"])]
    private ?string $ipAddress = null;

    #[ORM\Column(name: "externalIpAddress", type: "string", length: 13, nullable: true, options: ["default" => "NULL"])]
    private ?string $externalIpAddress = null;

    private ?string $secret = null;

    public function getDeviceID(): int
    {
        return $this->deviceID;
    }

    public function getUserID(): int
    {
        return $this->deviceID;
    }

    public function setDeviceID(int $deviceID): void
    {
        $this->deviceID = $deviceID;
    }

    public function getDeviceName(): ?string
    {
        return $this->deviceName;
    }

    public function setDeviceName(?string $deviceName): void
    {
        $this->deviceName = $deviceName;
    }

    public function getDeviceSecret(): ?string
    {
        return $this->secret;
    }

    public function setDeviceSecret(string $secret): void
    {
        $this->secret = $secret;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $secret): self
    {
        $this->password = $secret;

        return $this;
    }

    public function getCreatedById(): ?int
    {
        return $this->createdBy->getUserID();
    }

    public function getCreatedBy(): User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(User $createdBy): void
    {
        $this->createdBy = $createdBy;
    }

    public function getGroupObject(): Group
    {
        return $this->groupID;
    }

    public function setGroupObject(Group $groupID): void
    {
        $this->groupID = $groupID;
    }

    public function getRoomObject(): Room
    {
        return $this->roomID;
    }

    public function setRoomObject(Room $room): void
    {
        $this->roomID = $room;
    }

    #[ArrayShape(["0" => "ROLE_DEVICE"])]
    public function getRoles(): array
    {
        return array_unique($this->roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function getUsername(): string
    {
        return $this->deviceName;
    }

    public function eraseCredentials(): void
    {
    }

    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    public function setIpAddress(?string $ipAddress): void
    {
        $this->ipAddress = $ipAddress;
    }

    public function getExternalIpAddress(): ?string
    {
        return $this->externalIpAddress;
    }

    public function setExternalIpAddress(?string $externalIpAddress): void
    {
        $this->externalIpAddress = $externalIpAddress;
    }

    public function getUserIdentifier(): string
    {
        return $this->deviceName;
    }
}
