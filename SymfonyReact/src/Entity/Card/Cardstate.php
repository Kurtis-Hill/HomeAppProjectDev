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
    private $cardstateid;

    /**
     * @var string
     *
     * @ORM\Column(name="state", type="string", length=50, nullable=false)
     */
    private $state;

    /**
     * @return int
     */
    public function getCardstateid(): int
    {
        return $this->cardstateid;
    }

    /**
     * @param int $cardstateid
     */
    public function setCardstateid(int $cardstateid): void
    {
        $this->cardstateid = $cardstateid;
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
