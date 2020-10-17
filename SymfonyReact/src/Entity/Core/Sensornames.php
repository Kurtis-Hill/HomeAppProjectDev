<?php

namespace App\Entity\Core;

use App\Entity\Core\Groupname;
use App\Entity\Core\Room;
use App\Entity\Core\Sensortype;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sensornames
 *
 * @ORM\Table(name="sensornames", indexes={@ORM\Index(name="Room", columns={"roomID"}), @ORM\Index(name="GroupName", columns={"groupNameID"}), @ORM\Index(name="SensorType", columns={"sensorTypeID"})})
 * @ORM\Entity(repositoryClass="App\Repository\Core\SensorNamesRepository")
 */
class Sensornames
{
    /**
     * @var int
     *
     * @ORM\Column(name="sensorNameID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $sensornameid;

    /**
     * @var string
     *
     * @ORM\Column(name="sensorName", type="string", length=20, nullable=false)
     */
    private $sensorname;

    /**
     * @var Groupname
     *
     * @ORM\ManyToOne(targetEntity="Groupname")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="groupNameID", referencedColumnName="groupNameID")
     * })
     */
    private $groupnameid;

    /**
     * @var Room
     *
     * @ORM\ManyToOne(targetEntity="Room")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="roomID", referencedColumnName="roomID")
     * })
     */
    private $roomid;

    /**
     * @var Sensortype
     *
     * @ORM\ManyToOne(targetEntity="Sensortype")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sensorTypeID", referencedColumnName="sensorTypeID")
     * })
     */
    private $sensortypeid;

    /**
     * @var Devices
     *
     * @ORM\ManyToOne(targetEntity="Devices")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="deviceNameID", referencedColumnName="deviceNameID")
     * })
     */
    private $devicenameid;

    /**
     * @return Devices
     */
    public function getDevicenameid(): Devices
    {
        return $this->devicenameid;
    }

    /**
     * @param Devices $devicenameid
     */
    public function setDevicenameid(Devices $devicenameid): void
    {
        $this->devicenameid = $devicenameid;
    }

    public function getSensornameid(): ?int
    {
        return $this->sensornameid;
    }

    public function getSensorname(): ?string
    {
        return $this->sensorname;
    }

    public function setSensorname(string $sensorname): self
    {
        $this->sensorname = $sensorname;

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

    public function getSensortypeid(): ?Sensortype
    {
        return $this->sensortypeid;
    }

    public function setSensortypeid(?Sensortype $sensortypeid): self
    {
        $this->sensortypeid = $sensortypeid;

        return $this;
    }


}
