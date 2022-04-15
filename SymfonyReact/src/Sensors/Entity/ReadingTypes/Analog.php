<?php

namespace App\Sensors\Entity\ReadingTypes;

use App\Sensors\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorType;
use App\Sensors\Entity\SensorTypes\Soil;
use App\Sensors\Forms\CustomFormValidatos\SensorDataValidators\SoilConstraint;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Table(name="analog", uniqueConstraints={@ORM\UniqueConstraint(name="analog_ibfk_3", columns={"sensorNameID"})})
 * @ORM\Entity(repositoryClass="App\Sensors\Repository\ORM\ReadingType\AnalogRepository")
 */
class Analog extends AbstractReadingType implements StandardReadingSensorInterface, AllSensorReadingTypeInterface
{
    public const READING_TYPE = 'analog';

    public const ANALOG_SENSORS = [
        Soil::NAME
    ];

    /**
     * @ORM\Column(name="analogID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $analogID;

    /**
     * @ORM\Column(name="analogReading", type="smallint", nullable=true, options={"default"="NULL"})
     */
    #[SoilConstraint(groups: [Soil::NAME])]
    private float $analogReading;

    /**
     * @ORM\Column(name="highAnalog", type="smallint", nullable=true, options={"default"="1000"})
     */
    #[SoilConstraint(groups: [Soil::NAME])]
    private int $highAnalog = 9999;

    /**
     * @ORM\Column(name="lowAnalog", type="smallint", nullable=true, options={"default"="1000"})
     */
    #[SoilConstraint(groups: [Soil::NAME])]
    private int $lowAnalog = 1111;

    /**
     * @ORM\Column(name="constRecord", type="boolean", nullable=true)
     */
    #[Assert\Type("bool")]
    private bool $constRecord = false;

    /**
     * @ORM\Column(name="updatedAt", type="datetime", nullable=false, options={"default"="current_timestamp()"})
     */
    #[Assert\NotBlank(message: 'analog date time should not be blank')]
    private DateTimeInterface $updatedAt;


    /**
     * @ORM\ManyToOne(targetEntity="App\Sensors\Entity\Sensor")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="sensorNameID", referencedColumnName="sensorNameID")
     * })
     */
    private Sensor $sensorNameID;

    public function getSensorID(): int
    {
        return $this->analogID;
    }


    public function setSensorID(int $analogid): void
    {
        $this->analogID = $analogid;
    }


    public function getSensorNameID(): Sensor
    {
        return $this->sensorNameID;
    }

    /**
     * @param Sensor $sensorNameID
     */
    public function setSensorObject(Sensor $sensorNameID): void
    {
        $this->sensorNameID = $sensorNameID;
    }

    /**
     * Sensor Reading Methods
     */

    public function getCurrentReading(): int|float
    {
        return $this->analogReading;
    }

    public function getHighReading(): int|float
    {
        return $this->highAnalog;
    }

    public function getLowReading(): int|float
    {
        return $this->lowAnalog;
    }

    public function getUpdatedAt(): DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setCurrentReading(int|float|string $currentReading): void
    {
        $this->analogReading = $currentReading;
    }

    public function setHighReading(int|float|string $reading): void
    {
        if (is_numeric($reading)) {
            $this->highAnalog = $reading;
        }
    }

    public function setLowReading(int|float|string $reading): void
    {
        if (is_numeric($reading)) {
            $this->lowAnalog = $reading;
        }
    }

    public function setUpdatedAt(): void
    {
        $this->updatedAt = new DateTimeImmutable('now');
    }

    public function getConstRecord(): bool
    {
        return $this->constRecord;
    }

    public function setConstRecord(bool $constRecord): void
    {
        $this->constRecord = $constRecord;
    }

    public function getReadingType(): string
    {
        return self::READING_TYPE;
    }

    #[Assert\Callback(groups: [Soil::NAME])]
    public function validate(ExecutionContextInterface $context): void
    {
        if ($this->getHighReading() < $this->getLowReading()) {
            $context
                ->buildViolation('High reading for ' . $this->getReadingType() . ' cannot be lower than low reading')
                ->addViolation();
        }
    }
}