<?php

namespace App\ESPDeviceSensor\Entity\ReadingTypes;

use App\ESPDeviceSensor\Entity\ReadingTypes\Analog;
use App\ESPDeviceSensor\Entity\ReadingTypes\Humidity;
use App\ESPDeviceSensor\Entity\ReadingTypes\Latitude;
use App\ESPDeviceSensor\Entity\ReadingTypes\Temperature;
use Doctrine\ORM\Mapping as ORM;

//@TODO change to sensortype
/**
 * @ORM\Table(name="ReadingTypes")
 * @ORM\Entity(repositoryClass="App\ESPDeviceSensor\Repository\ORM\SensorReadingType\ReadingTypeRepository")
 */
class ReadingTypes
{
    public const SENSOR_READING_TYPE_DATA = [
        Temperature::READING_TYPE => [
            'alias' => 'temp',
            'object' => Temperature::class,
        ],
        Humidity::READING_TYPE => [
            'alias' => 'humid',
            'object' => Humidity::class,
        ],
        Analog::READING_TYPE => [
            'alias' => 'analog',
            'object' => Analog::class,
        ],
        Latitude::READING_TYPE => [
            'alias' => 'lat',
            'object' => Latitude::class,
        ],
    ];

    /**
     * @ORM\Column(name="readingTypeID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $readingTypeID;

    /**
     * @ORM\Column(name="readingType", type="string", length=50, nullable=false)
     */
    private string $readingType;

    public function getReadingTypeID(): int
    {
        return $this->readingTypeID;
    }

    public function setReadingTypeID(int $readingTypeID): void
    {
        $this->readingTypeID = $readingTypeID;
    }

    public function getReadingType(): string
    {
        return $this->readingType;
    }

    public function setReadingType(string $readingType): void
    {
        $this->readingType = $readingType;
    }
}
