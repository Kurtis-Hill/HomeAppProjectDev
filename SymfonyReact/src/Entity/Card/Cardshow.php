<?php

namespace App\Entity\Card;

use App\Entity\Card\Cardstate;
use Doctrine\ORM\Mapping as ORM;

/**
 * Cardshow
 *
 * @ORM\Table(name="cardshow", indexes={@ORM\Index(name=" cardshow_ibfk_2", columns={"roomPage"}), @ORM\Index(name="cardshow_ibfk_1", columns={"indexPage"})})
 * @ORM\Entity
 */
class Cardshow
{
    /**
     * @var int
     *
     * @ORM\Column(name="cardShowID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $cardshowid;

    /**
     * @var Cardstate
     *
     * @ORM\ManyToOne(targetEntity="Cardstate")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="roomPage", referencedColumnName="cardStateID")
     * })
     */
    private $roompage;

    /**
     * @var Cardstate
     *
     * @ORM\ManyToOne(targetEntity="Cardstate")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="indexPage", referencedColumnName="cardStateID")
     * })
     */
    private $indexpage;

    public function getCardshowid(): ?int
    {
        return $this->cardshowid;
    }

    public function getRoompage(): ?Cardstate
    {
        return $this->roompage;
    }

    public function setRoompage(?Cardstate $roompage): self
    {
        $this->roompage = $roompage;

        return $this;
    }

    public function getIndexpage(): ?Cardstate
    {
        return $this->indexpage;
    }

    public function setIndexpage(?Cardstate $indexpage): self
    {
        $this->indexpage = $indexpage;

        return $this;
    }


}
