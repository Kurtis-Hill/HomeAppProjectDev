<?php

namespace App\Entity\Sensor\ReadingTypes;

use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Motion;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Relay;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Analog;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Humidity;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Latitude;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Temperature;
use App\Repository\Sensor\SensorReadingType\ORM\ReadingTypeRepository;
use Doctrine\ORM\Mapping as ORM;

#[
    ORM\Entity(repositoryClass: ReadingTypeRepository::class),
    ORM\Table(name: "readingtypes"),
    ORM\UniqueConstraint(name: "readingType", columns: ["readingType"]),
]
class ReadingTypes
{
    //Add all new sensor reading types here
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
        Relay::READING_TYPE => [
            'alias' => 'relay',
            'object' => Relay::class,
        ],
        Motion::READING_TYPE => [
            'alias' => 'motion',
            'object' => Motion::class,
        ],
    ];

    public const ALL_READING_TYPES = [
        Temperature::READING_TYPE,
        Humidity::READING_TYPE,
        Analog::READING_TYPE,
        Latitude::READING_TYPE,
        Relay::READING_TYPE,
        Motion::READING_TYPE,
    ];

    #[
        ORM\Column(name: "readingTypeID", type: "integer", nullable: false),
        ORM\Id,
        ORM\GeneratedValue(strategy: "IDENTITY"),
    ]
    private int $readingTypeID;

    #[ORM\Column(name: "readingType", type: "string", length: 50, nullable: false)]
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
