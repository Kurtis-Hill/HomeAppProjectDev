<?php

namespace App\Entity\Sensors;

use App\Entity\Card\Cardshow;
use App\Entity\Card\Cardview;
use App\Entity\Core\Groupname;
use App\Entity\Core\Room;
use App\Entity\Core\Sensornames;
use App\HomeAppCore\Interfaces\StandardSensorInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Analog
 *
 * @ORM\Table(name="analog", indexes={@ORM\Index(name="groupNameID", columns={"groupNameID"}), @ORM\Index(name="analog_ibfk_4", columns={"cardShowID"}), @ORM\Index(name="roomID", columns={"roomID"}), @ORM\Index(name="analog_ibfk_3", columns={"sensorNameID"}), @ORM\Index(name="analog_ibfk_5", columns={"cardViewID"})})
 * @ORM\Entity(repositoryClass="App\Repository\Sensors\AnalogRepository")
 */
class Analog implements StandardSensorInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="analogID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $analogid;

    /**
     * @var float|null
     *
     * @ORM\Column(name="analogReading", type="float", precision=10, scale=0, nullable=true)
     */
    private $analogReading;

    /**
     * @var float|null
     *
     * @ORM\Column(name="highAnalog", type="float", precision=10, scale=0, nullable=true)
     */
    private $highAnalogReading;

    /**
     * @var float|null
     *
     * @ORM\Column(name="lowAnalog", type="float", precision=10, scale=0, nullable=true)
     */
    private $lowAnalogReading;

    /**
     * @var bool|null
     *
     * @ORM\Column(name="constRecord", type="boolean", nullable=true, options={"default"="1"})
     */
    private $constrecord = true;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timez", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $analogTime = 'CURRENT_TIMESTAMP';

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
     * @var Groupname
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\Groupname")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="groupNameID", referencedColumnName="groupNameID")
     * })
     */
    private $groupnameid;

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

    public function getAnalogid(): ?int
    {
        return $this->analogid;
    }

    public function getCurrentSensorReading(): ?float
    {
        return $this->analogReading;
    }

    public function setCurrentSensorReading($analogreading = null): self
    {
        $this->analogReading = $analogreading;

        return $this;
    }

    public function getHighReading(): ?float
    {
        return $this->highAnalogReading;
    }

    public function setHighReading($highanalog = null): self
    {
        $this->highAnalogReading = $highanalog;

        return $this;
    }

    public function getLowReading(): ?float
    {
        return $this->lowAnalogReading;
    }

    public function setLowReading($lowanalog = null): self
    {
        $this->lowAnalogReading = $lowanalog;

        return $this;
    }

    public function getConstrecord(): ?bool
    {
        return $this->constrecord;
    }

    public function setConstrecord(?bool $constrecord): self
    {
        $this->constrecord = $constrecord;

        return $this;
    }

    public function getTime(): ?\DateTimeInterface
    {
        return $this->analogTime;
    }

    public function setTime(\DateTimeInterface $timez): self
    {
        $this->analogTime = $timez;

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

    public function getGroupnameid(): ?Groupname
    {
        return $this->groupnameid;
    }

    public function setGroupnameid(?Groupname $groupnameid): self
    {
        $this->groupnameid = $groupnameid;

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
