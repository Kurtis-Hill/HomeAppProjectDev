<?php

namespace App\Authentication\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * RefreshTokens
 *
 * @ORM\Table(name="refresh_tokens", uniqueConstraints={@ORM\UniqueConstraint(name="UNIQ_9BACE7E1C74F2195", columns={"refresh_token"})})
 * @ORM\Entity
 */
class RefreshTokens
{
    /**
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $id;

    /**
     * @ORM\Column(name="refresh_token", type="string", length=128, nullable=false)
     */
    private string $refreshToken;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255, nullable=false)
     */
    private string $username;

    /**
     * @ORM\Column(name="valid", type="datetime", nullable=false)
     */
    private DateTime $valid;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    public function setRefreshToken(string $refreshToken): void
    {
        $this->refreshToken = $refreshToken;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getValid(): DateTime
    {
        return $this->valid;
    }

    public function setValid(DateTime $valid): void
    {
        $this->valid = $valid;
    }
}
