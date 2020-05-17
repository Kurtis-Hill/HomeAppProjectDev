<?php

namespace App\Entity\Sensors;

use App\Entity\Card\Cardshow;
use App\Entity\Card\Cardstate;
use App\Entity\Card\Cardview;
use App\Entity\Core\Groupname;
use App\Entity\Core\Room;
use App\Entity\Core\Sensornames;
use Doctrine\ORM\Mapping as ORM;

/**
 * Humid
 *
 * @ORM\Table(name="humid", indexes={@ORM\Index(name="GroupName", columns={"groupNameID"}), @ORM\Index(name="humid_ibfk_4", columns={"cardstateid"}), @ORM\Index(name="Room", columns={"roomID"}), @ORM\Index(name="humid_ibfk_3", columns={"sensorNameID"}), @ORM\Index(name="humid_ibfk_5", columns={"cardViewID"})})
 * @ORM\Entity(repositoryClass="App\Repository\Sensors\HumidRepository")
 */
class Humid
{
    /**
     * @var int
     *
     * @ORM\Column(name="humidID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $humidid;

    /**
     * @var float
     *
     * @ORM\Column(name="humidReading", type="float", precision=10, scale=0, nullable=false)
     */
    private $humidreading;

    /**
     * @var float
     *
     * @ORM\Column(name="highHumid", type="float", precision=10, scale=0, nullable=false, options={"default"="70"})
     */
    private $highhumid = '70';

    /**
     * @var float
     *
     * @ORM\Column(name="lowHumid", type="float", precision=10, scale=0, nullable=false, options={"default"="15"})
     */
    private $lowhumid = '15';

    /**
     * @var bool
     *
     * @ORM\Column(name="constRecord", type="boolean", nullable=false)
     */
    private $constrecord = '0';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timez", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $timez = 'CURRENT_TIMESTAMP';

    /**
     * @var Groupname
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\Groupname")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="groupNameID", referencedColumnName="groupNameID")
     * })
     */
    private $groupnameid;

    /**
     * @var Room
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\Room")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="roomID", referencedColumnName="roomID")
     * })
     */
    private $roomid;

    /**
     * @var Sensornames
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\Sensornames")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sensorNameID", referencedColumnName="sensorNameID")
     * })
     */
    private $sensornameid;

    /**
     * @var Cardshow
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Card\Cardstate")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cardStateID", referencedColumnName="cardStateID")
     * })
     */
    private $cardstateid;

    /**
     * @var Cardview
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Card\Cardview")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cardViewID", referencedColumnName="cardViewID")
     * })
     */
    private $cardviewid;

    public function getHumidid(): ?int
    {
        return $this->humidid;
    }

    public function getHumidreading(): ?float
    {
        return $this->humidreading;
    }

    public function setHumidreading(float $humidreading): self
    {
        $this->humidreading = $humidreading;

        return $this;
    }

    public function getHighhumid(): ?float
    {
        return $this->highhumid;
    }

    public function setHighhumid(float $highhumid): self
    {
        $this->highhumid = $highhumid;

        return $this;
    }

    public function getLowhumid(): ?float
    {
        return $this->lowhumid;
    }

    public function setLowhumid(float $lowhumid): self
    {
        $this->lowhumid = $lowhumid;

        return $this;
    }

    public function getConstrecord(): ?bool
    {
        return $this->constrecord;
    }

    public function setConstrecord(bool $constrecord): self
    {
        $this->constrecord = $constrecord;

        return $this;
    }

    public function getTimez(): ?\DateTimeInterface
    {
        return $this->timez;
    }

    public function setTimez(\DateTimeInterface $timez): self
    {
        $this->timez = $timez;

        return $this;
    }

    public function getGroupnameid(): ?Groupname
    {
        return $this->groupnameid;
    }

    public function setGroupnameid(?Groupname $groupnameid): self
    {
        $this->groupnameid = $groupnameid;

        return $this;
    }

    public function getRoomid(): ?Room
    {
        return $this->roomid;
    }

    public function setRoomid(?Room $roomid): self
    {
        $this->roomid = $roomid;

        return $this;
    }

    public function getSensornameid(): ?Sensornames
    {
        return $this->sensornameid;
    }

    public function setSensornameid(?Sensornames $sensornameid): self
    {
        $this->sensornameid = $sensornameid;

        return $this;
    }

    public function getcardstateid(): ?Cardstate
    {
        return $this->cardstateid;
    }

    public function setcardstateid(?Cardshow $cardstateid): self
    {
        $this->cardstateid = $cardstateid;

        return $this;
    }

    public function getCardviewid(): ?Cardview
    {
        return $this->cardviewid;
    }

    public function setCardviewid(?Cardview $cardviewid): self
    {
        $this->cardviewid = $cardviewid;

        return $this;
    }


}
