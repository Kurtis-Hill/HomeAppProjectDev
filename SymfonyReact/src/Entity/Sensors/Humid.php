<?php

namespace App\Entity\Sensors;


use App\Entity\Card\Cardview;
use App\Entity\Core\Groupname;
use App\Entity\Core\Room;
use App\Entity\Core\Sensornames;
use App\HomeAppCore\Interfaces\StandardSensorInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Humid
 *
 * @ORM\Table(name="humid", indexes={@ORM\Index(name="GroupName", columns={"groupNameID"}), @ORM\Index(name="humid_ibfk_4"), @ORM\Index(name="Room", columns={"roomID"}), @ORM\Index(name="humid_ibfk_3", columns={"sensorNameID"}), @ORM\Index(name="humid_ibfk_5", columns={"cardViewID"})})
 * @ORM\Entity(repositoryClass="App\Repository\Sensors\HumidRepository")
 */
class Humid implements StandardSensorInterface
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
    private $humidReading;

    /**
     * @var float
     *
     * @ORM\Column(name="highHumid", type="float", precision=10, scale=0, nullable=false, options={"default"="70"})
     */
    private $highHumidReading = '70';

    /**
     * @var float
     *
     * @ORM\Column(name="lowHumid", type="float", precision=10, scale=0, nullable=false, options={"default"="15"})
     */
    private $lowHumidReading = '15';

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
    private $humidTime = 'CURRENT_TIMESTAMP';

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

    public function getCurrentSensorReading(): ?float
    {
        return $this->humidReading;
    }

    public function setCurrentSensorReading($humidreading = null): self
    {
        $this->humidReading = $humidreading;

        return $this;
    }

    public function getHighReading(): ?float
    {
        return $this->highHumidReading;
    }

    public function setHighReading($highhumid = null): self
    {
        $this->highHumidReading = $highhumid;

        return $this;
    }

    public function getLowReading(): ?float
    {
        return $this->lowHumidReading;
    }

    public function setLowReading($lowhumid = null): self
    {
        $this->lowHumidReading = $lowhumid;

        return $this;
    }

    public function getConstrecord(): ?bool
    {
        return $this->constrecord;
    }

    public function setConstrecord($constrecord): self
    {
        $constrecord = ($constrecord == "true") ? true : false;
        $this->constrecord = $constrecord;

        return $this;
    }

    public function getTime(): ?\DateTimeInterface
    {
        return $this->humidTime;
    }

    public function setTime(\DateTimeInterface $timez): self
    {
        $this->humidTime = $timez;

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
