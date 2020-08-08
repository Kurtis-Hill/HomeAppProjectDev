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

    const ON = 1;

    const OFF = 2;

    const INDEX_ONLY = 6;

    const ROOM_ONLY = 7;

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
     * @return int|null
     */
    public function getCardstateid(): ?int
    {
        return $this->cardstateid;
    }

    /**
     * @return string|null
     */
    public function getState(): ?string
    {
        return $this->state;
    }

    /**
     * @param string $state
     * @return Cardstate
     */
    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }


}
