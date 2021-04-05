<?php

namespace App\Entity\Card;

use Doctrine\ORM\Mapping as ORM;

/**
 * Cardstate
 *
 * @ORM\Table(name="cardstate")
 * @ORM\Entity(repositoryClass="App\Repository\Card\CardstateRepository")
 */
class Cardstate
{
    public const ON = 1;

    public const OFF = 2;

    public const INDEX_ONLY = 3;

    public const ROOM_ONLY = 4;
    /**
     * @var int
     *
     * @ORM\Column(name="cardStateID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $cardStateID;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=50, nullable=false)
     */
    private string $state;

    /**
     * @return int
     */
    public function getCardstateID(): int
    {
        return $this->cardStateID;
    }

    /**
     * @param int $cardStateID
     */
    public function setCardstateID(int $cardStateID): void
    {
        $this->cardStateID = $cardStateID;
    }

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @param string $state
     */
    public function setState(string $state): void
    {
        $this->state = $state;
    }

}
