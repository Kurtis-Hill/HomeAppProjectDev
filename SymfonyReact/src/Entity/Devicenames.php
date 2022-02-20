<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Devicenames
 *
 * @ORM\Table(name="devicenames", indexes={@ORM\Index(name="roomID", columns={"roomID"}), @ORM\Index(name="createdBy", columns={"createdBy"}), @ORM\Index(name="groupNameID", columns={"groupNameID"})})
 * @ORM\Entity
 */
class Devicenames
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
     * @var string
     *
     * @ORM\Column(name="password", type="text", length=0, nullable=false)
     */
    private $password;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ipAddress", type="string", length=13, nullable=true, options={"default"="NULL"})
     */
    private $ipaddress = 'NULL';

    /**
     * @var string|null
     *
     * @ORM\Column(name="externalIpAddress", type="string", length=13, nullable=true, options={"default"="NULL"})
     */
    private $externalipaddress = 'NULL';

    /**
     * @var json
     *
     * @ORM\Column(name="roles", type="json", nullable=false)
     */
    private $roles;

    /**
     * @var \Groupname
     *
     * @ORM\ManyToOne(targetEntity="Groupname")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="groupNameID", referencedColumnName="groupNameID")
     * })
     */
    private $groupnameid;

    /**
     * @var \Room
     *
     * @ORM\ManyToOne(targetEntity="Room")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="roomID", referencedColumnName="roomID")
     * })
     */
    private $roomid;

    /**
     * @var \User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="createdBy", referencedColumnName="userID")
     * })
     */
    private $createdby;


}
