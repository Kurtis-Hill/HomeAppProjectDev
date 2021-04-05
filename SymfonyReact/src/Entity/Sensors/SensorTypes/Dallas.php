<?php

namespace App\Entity\Sensors\SensorTypes;

use App\Entity\Card\CardView;
use App\Entity\Sensors\ReadingTypes\Temperature;
use App\Entity\Sensors\Sensors;
use App\HomeAppSensorCore\Interfaces\SensorTypes\StandardSensorTypeInterface;
use App\HomeAppSensorCore\Interfaces\SensorTypes\TemperatureSensorTypeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * Dallas
 *
 * @ORM\Table(name="dallas", uniqueConstraints={@ORM\UniqueConstraint(name="tempID", columns={"tempID"}), @ORM\UniqueConstraint(name="cardViewID", columns={"cardViewID"})})
 * @ORM\Entity(repositoryClass="App\Repository\ReadingType\DallasRepository")
 */
class Dallas implements StandardSensorTypeInterface, TemperatureSensorTypeInterface
{
    public const MAX_POSSIBLE_SENSORS = 8;
    /**
     * @var int
     *
     * @ORM\Column(name="dallasID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $dallasID;

    /**
     * @var Temperature
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Sensors\ReadingTypes\Temperature")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tempID", referencedColumnName="tempID")
     * })
     */
    private Temperature $tempID;

    /**
     * @var Sensors
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Sensors\Sensors")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sensorNameID", referencedColumnName="sensorNameID")
     * })
     */
    private Sensors $sensor;


    /**
     * @var CardView
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Card\CardView")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="cardViewID", referencedColumnName="cardViewID")
     * })
     */
    private CardView $cardViewID;



    /**
     * @return int
     */
    public function getSensorTypeID(): int
    {
        return $this->dallasID;
    }

    /**
     * @param int $dallasID
     */
    public function setSensorTypeID(int $dallasID): void
    {
        $this->dallasID = $dallasID;
    }

    /**
     * @return CardView
     */
    public function getCardViewObject(): CardView
    {
        return $this->cardViewID;
    }

    /**
     * @param CardView $cardViewID
     */
    public function setCardViewObject(CardView $cardViewID): void
    {
        $this->cardViewID = $cardViewID;
    }

    /**
     * @return Temperature
     */
    public function getTempObject(): Temperature
    {
        return $this->tempID;
    }

    /**
     * @param Temperature $tempID
     */
    public function setTempObject(Temperature $tempID): void
    {
        $this->tempID = $tempID;
    }

    /**
     * @return Sensors
     */
    public function getSensor(): Sensors
    {
        return $this->sensor;
    }

    /**
     * @param Sensors $sensor
     */
    public function setSensor(Sensors $sensor): void
    {
        $this->sensor = $sensor;
    }




}
