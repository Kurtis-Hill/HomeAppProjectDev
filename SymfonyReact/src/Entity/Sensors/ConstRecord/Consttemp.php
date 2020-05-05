<?php

namespace App\Entity\Sensors\ConstRecord;

use App\Entity\Core\Groupname;
use App\Entity\Core\Room;
use App\Entity\Core\Sensornames;
use Doctrine\ORM\Mapping as ORM;

/**
 * Consttemp
 *
 * @ORM\Table(name="consttemp", indexes={@ORM\Index(name="GroupName", columns={"groupNameID"}), @ORM\Index(name="Room", columns={"roomID"}), @ORM\Index(name="SensorName", columns={"sensorNameID"})})
 * @ORM\Entity
 */
class Consttemp
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
     * @ORM\Column(name="tempReading", type="float", precision=10, scale=0, nullable=true)
     */
    private $tempreading;

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

    public function getTempid(): ?int
    {
        return $this->tempid;
    }

    public function getTempreading(): ?float
    {
        return $this->tempreading;
    }

    public function setTempreading(?float $tempreading): self
    {
        $this->tempreading = $tempreading;

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


}
