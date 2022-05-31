<?php

namespace App\User\Entity;

use App\Common\Form\CustomFormValidators\NoSpecialCharactersConstraint;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * GroupNames.
 *
 * @ORM\Table(name="groupname", uniqueConstraints={@ORM\UniqueConstraint(name="groupName", columns={"groupName"})})
 * @ORM\Entity(repositoryClass="App\User\Repository\ORM\GroupNameRepository")
 */
#[UniqueEntity('groupName')]
class GroupNames
{
    public const NOT_PART_OF_THIS_GROUP_ERROR_MESSAGE = 'You are not part of this group';

    private const GROUP_NAME_MIN_LENGTH = 2;

    private const GROUP_NAME_MAX_LENGTH = 50;

    /**
     * @var int
     *
     * @ORM\Column(name="groupNameID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $groupNameID;

    /**
     * @var string
     *
     * @ORM\Column(name="groupName", type="string", length=50, nullable=false)
     */
    #[
        NoSpecialCharactersConstraint,
        Assert\Length(
            min: self::GROUP_NAME_MIN_LENGTH,
            max: self::GROUP_NAME_MAX_LENGTH,
            minMessage: "Group name must be at least {{ limit }} characters long",
            maxMessage: "Group name cannot be longer than {{ limit }} characters"
        ),
        Assert\NotBlank,
    ]
    private string $groupName;

    /**
     * @ORM\Column(name="createdAt", type="datetime", nullable=false, options={"default"="current_timestamp()"})
     */
    private DateTimeInterface $createdAt;

    public function getGroupNameID(): int
    {
        return $this->groupNameID;
    }

    public function setGroupNameID(int $groupNameID): void
    {
        $this->groupNameID = $groupNameID;
    }

    public function getGroupName(): string
    {
        return $this->groupName;
    }

    public function setGroupName(string $groupName): void
    {
        $this->groupName = $groupName;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(): void
    {
        $this->createdAt = new DateTimeImmutable('now');
    }
}
