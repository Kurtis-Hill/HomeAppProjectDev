<?php

namespace App\Devices\Entity;

use App\Core\UserInterface\APISensorUserInterface;
use App\Form\CustomFormValidators as NoSpecialCharacters;
use App\User\Entity\GroupNames;
use App\User\Entity\Room;
use App\User\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Devices
 *
 * @ORM\Table(name="devicenames", indexes={@ORM\Index(name="createdBy", columns={"createdBy"}), @ORM\Index(name="groupNameID", columns={"groupNameID"}), @ORM\Index(name="roomID", columns={"roomID"})})
 * @ORM\Entity(repositoryClass="App\Repository\Core\DevicesRepository")
 */
class Devices implements UserInterface, APISensorUserInterface
{
    private const DEVICE_MIN_LENGTH = 2;

    private const DEVICE_MAX_LENGTH = 20;

    public const ROLE = 'ROLE_DEVICE';

    /**
     * @var int
     *
     * @ORM\Column(name="deviceNameID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $deviceNameID;

    /**
     * @ORM\Column(name="deviceName", type="string", length=20, nullable=false)
     */
    #[
        NoSpecialCharacters\NoSpecialCharactersConstraint,
        Assert\NotBlank(
            message: 'Device name should not be blank'
        ),
        Assert\Length(
            min: self::DEVICE_MIN_LENGTH,
            max: self::DEVICE_MAX_LENGTH,
            minMessage: "Device name must be at least {{ limit }} characters long",
            maxMessage: "Device name cannot be longer than {{ limit }} characters"
        )
    ]
    private ?string $deviceName;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="text", length=100, nullable=false)
     */
    private string $password;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="App\User\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="createdBy", referencedColumnName="userID")
     * })
     */
    private User $createdBy;

    /**
     * @var GroupNames
     *
     * @ORM\ManyToOne(targetEntity="App\User\Entity\GroupNames")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="groupNameID", referencedColumnName="groupNameID")
     * })
     */
    #[Assert\NotBlank(message: 'Group name should not be blank')]
    private GroupNames $groupNameID;

    /**
     * @var Room
     *
     * @ORM\ManyToOne(targetEntity="App\User\Entity\Room")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="roomID", referencedColumnName="roomID")
     * })
     */
    #[Assert\NotBlank(message: 'Room should not be blank')]
    private Room $roomID;

    /**
     * @var array
     *
     * @ORM\Column(name="roles", type="json", nullable=false)
     */
    private array $roles;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ipAddress", type="string", length=13, nullable=true, options={"default"="NULL"})
     */
    private ?string $ipAddress = null;

    /**
     * @var string|null
     *
     * @ORM\Column(name="externalIpAddress", type="string", length=13, nullable=true, options={"default"="NULL"})
     */
    private ?string $externalIpAddress = null;

    private string $secret;

    private array $userGroupMappingEntities = [];


    public function getUserGroupMappingEntities(): array
    {
        return $this->userGroupMappingEntities;
    }

    public function setUserGroupMappingEntities(array $userGroupMappingEntities): void
    {
        $this->userGroupMappingEntities = $userGroupMappingEntities;
    }

    public function getGroupNameIds(): array
    {
        $groupNames = [];
        foreach ($this->userGroupMappingEntities as $entity) {
            $groupNames[] = $entity->getGroupNameID()->getGroupNameID();
        }

        return $groupNames;
    }

    public function getDeviceNameID(): int
    {
        return $this->deviceNameID;
    }

    public function getUserID(): int
    {
        return $this->deviceNameID;
    }

    public function setDeviceNameID(int $deviceNameID): void
    {
        $this->deviceNameID = $deviceNameID;
    }

    public function getDeviceName(): ?string
    {
        return $this->deviceName;
    }

    public function setDeviceName(?string $deviceName): void
    {
        $this->deviceName = $deviceName;
    }

    public function getDeviceSecret(): string
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

    #[Pure]
    public function getCreatedById(): int
    {
        if ($this->createdBy instanceof User) {
            return $this->createdBy->getUserID();
        }

        return $this->createdBy;
    }

    public function getCreatedBy(): User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(User $createdBy): void
    {
        $this->createdBy = $createdBy;
    }

    public function getGroupNameObject(): GroupNames
    {
        return $this->groupNameID;
    }

    public function setGroupNameObject(GroupNames $groupNameID): void
    {
        $this->groupNameID = $groupNameID;
    }

    public function getRoomObject(): Room
    {
        return $this->roomID;
    }

    public function setRoomObject(Room $room): void
    {
        $this->roomID = $room;
    }

    public function getRoles(): array
    {
        return array_unique($this->roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function getUsername(): string
    {
        return $this->deviceName;
    }


    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * @return string|null
     */
    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    /**
     * @param ?string $ipAddress
     */
    public function setIpAddress(?string $ipAddress): void
    {
        $this->ipAddress = $ipAddress;
    }

    /**
     * @return string|null
     */
    public function getExternalIpAddress(): ?string
    {
        return $this->externalIpAddress;
    }

    /**
     * @param string|null $externalIpAddress
     */
    public function setExternalIpAddress(?string $externalIpAddress): void
    {
        $this->externalIpAddress = $externalIpAddress;
    }
}
