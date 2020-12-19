<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Soil
 *
 * @ORM\Table(name="soil", uniqueConstraints={@ORM\UniqueConstraint(name="analogID", columns={"analogID"}), @ORM\UniqueConstraint(name="cardViewID", columns={"cardViewID"})})
 * @ORM\Entity
 */
class Soil
{
    /**
     * @var int
     *
     * @ORM\Column(name="soilID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $soilid;

    /**
     * @var \Analog
     *
     * @ORM\ManyToOne(targetEntity="Analog")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="analogID", referencedColumnName="analogID")
     * })
     */
    private $analogid;

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
     * @return int
     */
    public function getSoilid(): int
    {
        return $this->soilid;
    }

    /**
     * @param int $soilid
     */
    public function setSoilid(int $soilid): void
    {
        $this->soilid = $soilid;
    }

    /**
     * @return \Analog
     */
    public function getAnalogid(): \Analog
    {
        return $this->analogid;
    }

    /**
     * @param \Analog $analogid
     */
    public function setAnalogid(\Analog $analogid): void
    {
        $this->analogid = $analogid;
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


}
