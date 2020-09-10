<?php


namespace App\Entity\Core;

use Doctrine\ORM\Mapping as ORM;

/**
 * Room
 *
 * @ORM\Table(name="devicenames", indexes={@ORM\Index(name="Devices", columns={"deviceName"})})
 * @ORM\Entity(repositoryClass="App\Repository\Core\DevicesRepository")
 */
class Devices
{
    /**
     * @var int
     *
     * @ORM\Column(name="deviceNameID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $devicenameid;

    /**
     * @var string
     *
     * @ORM\Column(name="deviceName", type="string", length=20, nullable=false)
     */
    private $devicename;

    /**
     * @var int
     *
     * @ORM\ManyToOne(targetEntity="Groupname")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="groupNameID", referencedColumnName="groupNameID")
     * })
     */
    private $groupnameid;

    /**
     * @return int
     */
    public function getDevicenameid(): int
    {
        return $this->devicenameid;
    }

    public function getGroupNameid():int
    {
        return $this->groupnameid;
    }

    /**
     * @param int $devicenameid
     */
    public function setDevicenameid(int $devicenameid): void
    {
        $this->devicenameid = $devicenameid;
    }

    /**
     * @param string $devicename
     */
    public function setDevicename(string $devicename): void
    {
        $this->devicename = $devicename;
    }

    /**
     * @return string
     */
    public function getDevicename(): string
    {
        return $this->devicename;
    }

}