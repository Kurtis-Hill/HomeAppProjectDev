<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Dhtsensor
 *
 * @ORM\Table(name="dhtsensor", uniqueConstraints={@ORM\UniqueConstraint(name="tempID", columns={"tempID"}), @ORM\UniqueConstraint(name="humidID", columns={"humidID"}), @ORM\UniqueConstraint(name="cardviewID", columns={"cardviewID"})})
 * @ORM\Entity
 */
class Dhtsensor
{
    /**
     * @var int
     *
     * @ORM\Column(name="dhtID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $dhtid;

    /**
     * @var int
     *
     * @ORM\Column(name="tempID", type="integer", nullable=false)
     */
    private $tempid;

    /**
     * @var \Cardview
     *
     * @ORM\ManyToOne(targetEntity="Cardview")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cardviewID", referencedColumnName="cardViewID")
     * })
     */
    private $cardviewid;

    /**
     * @var \Humid
     *
     * @ORM\ManyToOne(targetEntity="Humid")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="humidID", referencedColumnName="humidID")
     * })
     */
    private $humidid;

    /**
     * @return int
     */
    public function getDhtid(): int
    {
        return $this->dhtid;
    }

    /**
     * @param int $dhtid
     */
    public function setDhtid(int $dhtid): void
    {
        $this->dhtid = $dhtid;
    }

    /**
     * @return int
     */
    public function getTempid(): int
    {
        return $this->tempid;
    }

    /**
     * @param int $tempid
     */
    public function setTempid(int $tempid): void
    {
        $this->tempid = $tempid;
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
     * @return \Humid
     */
    public function getHumidid(): \Humid
    {
        return $this->humidid;
    }

    /**
     * @param \Humid $humidid
     */
    public function setHumidid(\Humid $humidid): void
    {
        $this->humidid = $humidid;
    }


}
