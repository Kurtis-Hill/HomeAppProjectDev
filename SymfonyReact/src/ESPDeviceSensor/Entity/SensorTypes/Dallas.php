<?php

namespace App\ESPDeviceSensor\Entity\SensorTypes;

use App\ESPDeviceSensor\Entity\ReadingTypes\Temperature;
use App\ESPDeviceSensor\Entity\Sensor;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\StandardSensorTypeInterface;
use App\ESPDeviceSensor\Entity\SensorTypes\Interfaces\TemperatureSensorTypeInterface;
use App\UserInterface\Entity\Card\CardView;
use Doctrine\ORM\Mapping as ORM;

/**
 * Dallas
 *
 * @ORM\Table(name="dallas", uniqueConstraints={@ORM\UniqueConstraint(name="tempID", columns={"tempID"})}, indexes={@ORM\Index(name="sensorNameID", columns={"sensorNameID"})})
 * @ORM\Entity(repositoryClass="App\ESPDeviceSensor\Repository\ORM\SensorType\DallasRepository")
 */
class Dallas implements SensorTypeInterface, StandardSensorTypeInterface, TemperatureSensorTypeInterface
{
    public const NAME = 'Dallas';

    public const HIGH_TEMPERATURE_READING_BOUNDARY = 125;

    public const LOW_TEMPERATURE_READING_BOUNDARY = -55;

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
     * @ORM\ManyToOne(targetEntity="App\ESPDeviceSensor\Entity\ReadingTypes\Temperature", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tempID", referencedColumnName="tempID")
     * })
     */
    private Temperature $tempID;

    /**
     * @var Sensor
     *
     * @ORM\ManyToOne(targetEntity="App\ESPDeviceSensor\Entity\Sensor")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sensorNameID", referencedColumnName="sensorNameID", nullable=true)
     * })
     */
    private Sensor $sensorNameID;

    /**
     * @var CardView
     */
    private CardView $cardView;

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

    public function getSensorObject(): Sensor
    {
        return $this->sensorNameID;
    }

    public function setSensorObject(Sensor $sensor): void
    {
        $this->sensorNameID = $sensor;
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

    public function getCardViewObject(): ?CardView
    {
        return $this->cardView;
    }

    public function setCardViewObject(CardView $cardView): void
    {
        $this->cardView = $cardView;
    }

    public function getMaxTemperature(): float|int
    {
        return self::HIGH_TEMPERATURE_READING_BOUNDARY;
    }

    public function getMinTemperature(): float|int
    {
        return self::LOW_TEMPERATURE_READING_BOUNDARY;
    }

    public function getSensorTypeName(): string
    {
        return self::NAME;
    }
}
