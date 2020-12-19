<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Dallas
 *
 * @ORM\Table(name="dallas", uniqueConstraints={@ORM\UniqueConstraint(name="tempID", columns={"tempID"}), @ORM\UniqueConstraint(name="cardViewID", columns={"cardViewID"})})
 * @ORM\Entity
 */
class Dallas
{
    /**
     * @var int
     *
     * @ORM\Column(name="dallasID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $dallasid;

    /**
     * @var \Cardview
     *
     * @ORM\ManyToOne(targetEntity="Cardview")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cardViewID", referencedColumnName="cardViewID")
     * })
     */
    private $cardviewid;

    /**
     * @var \Temp
     *
     * @ORM\ManyToOne(targetEntity="Temp")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tempID", referencedColumnName="tempID")
     * })
     */
    private $tempid;

    /**
     * @return int
     */
    public function getDallasid(): int
    {
        return $this->dallasid;
    }

    /**
     * @param int $dallasid
     */
    public function setDallasid(int $dallasid): void
    {
        $this->dallasid = $dallasid;
    }

    /**
     * @return \Cardview
     */
    public function getCardviewid(): \Cardview
    {
        return $this->cardviewid;
    }

    /**
     * @param \Cardview $cardviewid
     */
    public function setCardviewid(\Cardview $cardviewid): void
    {
        $this->cardviewid = $cardviewid;
    }

    /**
     * @return \Temp
     */
    public function getTempid(): \Temp
    {
        return $this->tempid;
    }

    /**
     * @param \Temp $tempid
     */
    public function setTempid(\Temp $tempid): void
    {
        $this->tempid = $tempid;
    }


}
