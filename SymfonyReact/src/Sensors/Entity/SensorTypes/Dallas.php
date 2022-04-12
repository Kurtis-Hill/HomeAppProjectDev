<?php

namespace App\Sensors\Entity\SensorTypes;

use App\Sensors\Entity\ReadingTypes\Temperature;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\StandardSensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\TemperatureSensorTypeInterface;
use App\UserInterface\Entity\Card\CardView;
use Doctrine\ORM\Mapping as ORM;

/**
 * Dallas
 *
 * @ORM\Table(name="dallas", uniqueConstraints={@ORM\UniqueConstraint(name="tempID", columns={"tempID"})}, indexes={@ORM\Index(name="sensorNameID", columns={"sensorNameID"})})
 * @ORM\Entity(repositoryClass="App\Sensors\Repository\ORM\SensorType\DallasRepository")
 */
class Dallas implements SensorTypeInterface, StandardSensorTypeInterface, TemperatureSensorTypeInterface
{
    public const NAME = 'Dallas';

    public const ALIAS = 'dallas';

    public const HIGH_TEMPERATURE_READING_BOUNDARY = 125;

    public const LOW_TEMPERATURE_READING_BOUNDARY = -55;

    private const ALLOWED_READING_TYPES = [
        Temperature::READING_TYPE
    ];

    /**
     * @ORM\Column(name="dallasID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $dallasID;

    /**
     * @ORM\ManyToOne(targetEntity="App\Sensors\Entity\ReadingTypes\Temperature", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="tempID", referencedColumnName="tempID")
     * })
     */
    private Temperature $tempID;

    /**
     * @ORM\ManyToOne(targetEntity="App\Sensors\Entity\Sensor")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sensorNameID", referencedColumnName="sensorNameID", nullable=true)
     * })
     */
    private Sensor $sensorNameID;

    /**
     * @var CardView
     */
    private CardView $cardView;

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

    public function getTempObject(): Temperature
    {
        return $this->tempID;
    }

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

    public function getSensorTypeAlias(): string
    {
        return self::ALIAS;
    }

    public function getSensorClass(): string
    {
        return self::class;
    }

    public static function getAllowedReadingTypes(): array
    {
        return self::ALLOWED_READING_TYPES;
    }
}
