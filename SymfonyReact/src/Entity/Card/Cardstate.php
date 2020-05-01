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

    public function getCardstateid(): ?int
    {
        return $this->cardstateid;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): self
    {
        $this->state = $state;

        return $this;
    }


}
