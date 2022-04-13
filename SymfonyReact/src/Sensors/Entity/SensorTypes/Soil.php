<?php

namespace App\Sensors\Entity\SensorTypes;

use App\Sensors\Entity\ReadingTypes\Analog;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Interfaces\AnalogSensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\StandardSensorTypeInterface;
use App\UserInterface\Entity\Card\CardView;
use Doctrine\ORM\Mapping as ORM;

/**
 * Soil
 *
 * @ORM\Table(name="soil", uniqueConstraints={@ORM\UniqueConstraint(name="analogID", columns={"analogID"}), @ORM\UniqueConstraint(name="cardViewID", columns={"sensorNameID"})})
 * @ORM\Entity(repositoryClass="App\Sensors\Repository\ORM\SensorType\SoilRepository")
 */
class Soil implements SensorTypeInterface, StandardSensorTypeInterface, AnalogSensorTypeInterface
{
    public const NAME = 'Soil';

    public const ALIAS = 'soil';

    public const HIGH_SOIL_READING_BOUNDARY = 9999;

    public const LOW_SOIL_READING_BOUNDARY = 1000;

    private const ALLOWED_READING_TYPES = [
        Analog::READING_TYPE
    ];

    /**
     * @ORM\Column(name="soilID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $soilID;

    /**
     * @ORM\ManyToOne(targetEntity="App\Sensors\Entity\ReadingTypes\Analog")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="analogID", referencedColumnName="analogID")
     * })
     */
    private Analog $analogID;

    /**
     * @ORM\ManyToOne(targetEntity="App\Sensors\Entity\Sensor")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sensorNameID", referencedColumnName="sensorNameID", nullable=true)
     * })
     */
    private Sensor $sensorNameID;

    private CardView $cardView;

    /**
     * @return int
     */
    public function getSensorTypeID(): int
    {
        return $this->soilID;
    }

    /**
     * @param int $soilID
     */
    public function setSensorTypeID(int $soilID): void
    {
        $this->soilID = $soilID;
    }

    /**
     * @return Analog
     */
    public function getAnalogObject(): Analog
    {
        return $this->analogID;
    }

    /**
     * @param Analog $analogID
     */
    public function setAnalogObject(Analog $analogID): void
    {
        $this->analogID = $analogID;
    }

    /**
     * @return Sensor
     */
    public function getSensorObject(): Sensor
    {
        return $this->sensorNameID;
    }

    /**
     * @param Sensor $id
     */
    public function setSensorObject(Sensor $id): void
    {
        $this->sensorNameID = $id;
    }

    /**
     * @return CardView|null
     */
    public function getCardViewObject(): ?CardView
    {
        return $this->cardView;
    }

    /**
     * @param CardView $cardView
     */
    public function setCardViewObject(CardView $cardView): void
    {
        $this->cardView = $cardView;
    }

    public function getMaxAnalog(): float|int
    {
        return self::HIGH_SOIL_READING_BOUNDARY;
    }

    public function getMinAnalog(): float|int
    {
        return self::LOW_SOIL_READING_BOUNDARY;
    }

    public function getSensorTypeName(): string
    {
        return self::NAME;
    }

    public static function getSensorTypeAlias(): string
    {
        return self::ALIAS;
    }

    public static function getAllowedReadingTypes(): array
    {
        return self::ALLOWED_READING_TYPES;
    }
}
