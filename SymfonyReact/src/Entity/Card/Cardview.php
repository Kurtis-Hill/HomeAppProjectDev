<?php

namespace App\Entity;

use App\Entity\Card\Cardcolour;
use App\Entity\Card\Cardstate;
use App\Entity\Card\Icons;
use App\Entity\Core\Room;
use App\Entity\Core\User;
use App\Entity\Sensors\Sensors;
use Doctrine\ORM\Mapping as ORM;

/**
 * Cardview
 *
 * @ORM\Table(name="cardview", uniqueConstraints={@ORM\UniqueConstraint(name="sensorNameID", columns={"sensorNameID"})}, indexes={@ORM\Index(name="cardColour", columns={"cardColourID"}), @ORM\Index(name="cardState", columns={"cardStateID"}), @ORM\Index(name="Room", columns={"roomID"}), @ORM\Index(name="cardIcon", columns={"cardIconID"}), @ORM\Index(name="UserID", columns={"userID"}), @ORM\Index(name="cardview_show", columns={"cardViewID"})})
 * @ORM\Entity(repositoryClass="App\Repository\Card\CardviewRepository")
 */
class Cardview
{
    /**
     * @var int
     *
     * @ORM\Column(name="cardViewID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $cardviewid;

    /**
     * @var Sensors
     *
     * @ORM\ManyToOne(targetEntity="Sensors")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sensorNameID", referencedColumnName="sensorNameID")
     * })
     */
    private $sensornameid;

    /**
     * @var Cardstate
     *
     * @ORM\ManyToOne(targetEntity="Cardstate")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cardStateID", referencedColumnName="cardStateID")
     * })
     */
    private $cardstateid;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="userID", referencedColumnName="userID")
     * })
     */
    private $userid;

    /**
     * @var Icons
     *
     * @ORM\ManyToOne(targetEntity="Icons")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cardIconID", referencedColumnName="iconID")
     * })
     */
    private $cardiconid;

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
     * @var Cardcolour
     *
     * @ORM\ManyToOne(targetEntity="Cardcolour")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cardColourID", referencedColumnName="colourID")
     * })
     */
    private $cardcolourid;

    /**
     * @return int
     */
    public function getCardviewid(): int
    {
        return $this->cardviewid;
    }

    /**
     * @param int $cardviewid
     */
    public function setCardviewid(int $cardviewid): void
    {
        $this->cardviewid = $cardviewid;
    }

    /**
     * @return Sensors
     */
    public function getSensornameid(): Sensors
    {
        return $this->sensornameid;
    }

    /**
     * @param Sensors $sensornameid
     */
    public function setSensornameid(Sensors $sensornameid): void
    {
        $this->sensornameid = $sensornameid;
    }

    /**
     * @return Cardstate
     */
    public function getCardstateid(): Cardstate
    {
        return $this->cardstateid;
    }

    /**
     * @param Cardstate $cardstateid
     */
    public function setCardstateid(Cardstate $cardstateid): void
    {
        $this->cardstateid = $cardstateid;
    }

    /**
     * @return User
     */
    public function getUserid(): User
    {
        return $this->userid;
    }

    /**
     * @param User $userid
     */
    public function setUserid(User $userid): void
    {
        $this->userid = $userid;
    }

    /**
     * @return Icons
     */
    public function getCardiconid(): Icons
    {
        return $this->cardiconid;
    }

    /**
     * @param Icons $cardiconid
     */
    public function setCardiconid(Icons $cardiconid): void
    {
        $this->cardiconid = $cardiconid;
    }

    /**
     * @return Room
     */
    public function getRoomid(): Room
    {
        return $this->roomid;
    }

    /**
     * @param Room $roomid
     */
    public function setRoomid(Room $roomid): void
    {
        $this->roomid = $roomid;
    }

    /**
     * @return Cardcolour
     */
    public function getCardcolourid(): Cardcolour
    {
        return $this->cardcolourid;
    }

    /**
     * @param Cardcolour $cardcolourid
     */
    public function setCardcolourid(Cardcolour $cardcolourid): void
    {
        $this->cardcolourid = $cardcolourid;
    }



}
