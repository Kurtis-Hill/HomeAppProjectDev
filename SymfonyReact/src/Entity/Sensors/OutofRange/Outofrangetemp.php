<?php

namespace App\Entity\Sensors\OutofRange;

use App\Entity\Core\Groupname;
use App\Entity\Core\Room;
use App\Entity\Core\Sensornames;
use Doctrine\ORM\Mapping as ORM;

/**
 * Outofrangetemp
 *
 * @ORM\Table(name="outofrangetemp", indexes={@ORM\Index(name="GroupName", columns={"groupNameID"}), @ORM\Index(name="Room", columns={"roomID"}), @ORM\Index(name="SensorName", columns={"sensorNameID"})})
 * @ORM\Entity
 */
class Outofrangetemp
{
    /**
     * @var int
     *
     * @ORM\Column(name="tempID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $tempid;

    /**
     * @var float|null
     *
     * @ORM\Column(name="tempReadingID", type="float", precision=10, scale=0, nullable=true)
     */
    private $tempreadingid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="timez", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $timez = 'CURRENT_TIMESTAMP';

    /**
     * @var \Room
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\Room")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="roomID", referencedColumnName="roomID")
     * })
     */
    private $roomid;

    /**
     * @var \Groupname
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\Groupname")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="groupNameID", referencedColumnName="groupNameID")
     * })
     */
    private $groupnameid;

    /**
     * @var \Sensornames
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Core\Sensornames")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sensorNameID", referencedColumnName="sensorNameID")
     * })
     */
    private $sensornameid;

    public function getTempid(): ?int
    {
        return $this->tempid;
    }

    public function getTempreadingid(): ?float
    {
        return $this->tempreadingid;
    }

    public function setTempreadingid(?float $tempreadingid): self
    {
        $this->tempreadingid = $tempreadingid;

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


}
