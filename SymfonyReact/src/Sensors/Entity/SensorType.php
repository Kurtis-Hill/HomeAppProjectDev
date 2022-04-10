<?php

namespace App\Sensors\Entity;

use App\Sensors\Entity\ConstantRecording\ConstAnalog;
use App\Sensors\Entity\ConstantRecording\ConstHumid;
use App\Sensors\Entity\ConstantRecording\ConstLatitude;
use App\Sensors\Entity\ConstantRecording\ConstTemp;
use App\Sensors\Entity\OutOfRangeRecordings\OutOfRangeAnalog;
use App\Sensors\Entity\OutOfRangeRecordings\OutOfRangeHumid;
use App\Sensors\Entity\OutOfRangeRecordings\OutOfRangeLatitude;
use App\Sensors\Entity\OutOfRangeRecordings\OutOfRangeTemp;
use App\Sensors\Entity\ReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\Temperature;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\Soil;
use App\Sensors\Forms\StandardSensorOutOFBoundsForm;
use App\Sensors\Forms\UpdateReadingForm;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use App\Form\CustomFormValidators as NoSpecialCharacters;

/**
 * GetSensorTypesController
 *
 * @ORM\Table(name="sensortype", uniqueConstraints={@ORM\UniqueConstraint(name="sensorType", columns={"sensorType"})})
 * @ORM\Entity(repositoryClass="App\Sensors\Repository\ORM\Sensors\SensorTypeRepository")
 */
class SensorType
{
    public const ALIAS = 'sensortype';

    public const OUT_OF_BOUND_FORM_ARRAY_KEY = 'outOfBounds';

    public const UPDATE_CURRENT_READING_FORM_ARRAY_KEY = 'updateCurrentReading';

    // When creating a new sensor add a const here too and set it to the name of the entity
    public const DHT_SENSOR = 'Dht';

    public const BMP_SENSOR = 'Bmp';

    public const DALLAS_TEMPERATURE = 'Dallas';

    public const SOIL_SENSOR = 'Soil';

    // Used by service classes to create forms for the sensors and for getting data from the database e.g getting unknown sensor type object (described as object below)
    // to determine which sensor reading types are about to be updated
    // primarily used by the interface so if your sensor is going to have a view of some kind add it to this array
    public const ALL_SENSOR_TYPE_DATA = [
        Dht::NAME => [
            'alias' => 'dht',
            'object' => Dht::class,
            'readingTypes' => [
                'temperature' =>  Temperature::class,
                'humidity' => Humidity::class,
            ],
            'forms' => [
                self::OUT_OF_BOUND_FORM_ARRAY_KEY => [
                    'form' => StandardSensorOutOFBoundsForm::class,
                    'readingTypes' => [
                        Temperature::READING_TYPE =>  Temperature::class,
                        Humidity::READING_TYPE => Humidity::class,
                    ],
                ],
                self::UPDATE_CURRENT_READING_FORM_ARRAY_KEY => [
                    'form' => UpdateReadingForm::class,
                    'readingTypes' => [
                        Temperature::READING_TYPE =>  Temperature::class,
                        Humidity::READING_TYPE => Humidity::class,
                    ],
                ]
            ]
        ],

        SensorType::DALLAS_TEMPERATURE => [
            'alias' => 'dallas',
            'object' => Dallas::class,
            'readingTypes' => [
                Temperature::READING_TYPE =>  Temperature::class,
            ],
            'forms' => [
                self::OUT_OF_BOUND_FORM_ARRAY_KEY => [
                    'form' => StandardSensorOutOFBoundsForm::class,
                    'readingTypes' => [
                       Temperature::READING_TYPE =>  Temperature::class,
                    ],
                ],
                self::UPDATE_CURRENT_READING_FORM_ARRAY_KEY => [
                    'form' => UpdateReadingForm::class,
                    'readingTypes' => [
                        Temperature::READING_TYPE =>  Temperature::class,
                    ],
                ]
            ]
        ],

        Soil::NAME => [
            'alias' => 'soil',
            'object' => Soil::class,
            'readingTypes' => [
                Analog::READING_TYPE =>  Analog::class,
            ],
            'forms' => [
                self::OUT_OF_BOUND_FORM_ARRAY_KEY => [
                    'form' => StandardSensorOutOFBoundsForm::class,
                    'readingTypes' => [
                        Analog::READING_TYPE =>  Analog::class,
                    ],
                ],
                self::UPDATE_CURRENT_READING_FORM_ARRAY_KEY => [
                    'form' => UpdateReadingForm::class,
                    'readingTypes' => [
                        Analog::READING_TYPE =>  Analog::class,
                    ],
                ]
            ]
        ],

        Bmp::NAME => [
            'alias' => Bmp::NAME,
            'object' => Bmp::class,
            'readingTypes' => [
                'temperature' =>  Temperature::class,
                'humidity' => Humidity::class,
                'latitude' => Latitude::class,
            ],
            'forms' => [
                self::OUT_OF_BOUND_FORM_ARRAY_KEY => [
                    'form' => StandardSensorOutOFBoundsForm::class,
                    'readingTypes' => [
                        'temperature' =>  Temperature::class,
                        'humidity' =>  Humidity::class,
                        'latitude' => Latitude::class,
                    ],
                ],
                self::UPDATE_CURRENT_READING_FORM_ARRAY_KEY => [
                    'form' => UpdateReadingForm::class,
                    'readingTypes' => [
                        Temperature::READING_TYPE =>  Temperature::class,
                        'humidity' =>  Humidity::class,
                        'latitude' => Latitude::class,
                    ],
                ]
            ]
        ],
    ];

    public const SENSOR_READING_TYPE_DATA = [
        Sensor::TEMPERATURE => [
            'alias' => 'temp',
            'object' => Temperature::class,
            'outOfBounds' => OutOfRangeTemp::class,
            'constRecord' => ConstTemp::class
        ],
        Sensor::HUMIDITY => [
            'alias' => 'humid',
            'object' => Humidity::class,
            'outOfBounds' => OutOfRangeHumid::class,
            'constRecord' => ConstHumid::class
        ],
        Sensor::ANALOG => [
            'alias' => 'analog',
            'object' => Analog::class,
            'outOfBounds' => OutOfRangeAnalog::class,
            'constRecord' => ConstAnalog::class
        ],
        Sensor::LATITUDE => [
            'alias' => 'lat',
            'object' => Latitude::class,
            'outOfBounds' => OutOfRangeLatitude::class,
            'constRecord' => ConstLatitude::class
        ],
    ];

    public const ALL_SENSOR_TYPES = [
        Bmp::NAME,
        Soil::NAME,
        Dallas::NAME,
        Dht::NAME,
    ];

    private const SENSOR_TYPE_DESCRIPTION_MIN_LENGTH = 5;

    private const SENSOR_TYPE_DESCRIPTION_MAX_LENGTH = 50;

    /**
     * @ORM\Column(name="sensorTypeID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $sensorTypeID;

    /**
     * @ORM\Column(name="sensorType", type="string", length=20, nullable=false)
     */
    #[NoSpecialCharacters\NoSpecialCharactersConstraint]
    private string $sensorType;

    /**
     * @ORM\Column(name="description", type="string", length=50, nullable=false)
     */
    #[
        NoSpecialCharacters\NoSpecialCharactersConstraint,
        Assert\Length(
            min: self::SENSOR_TYPE_DESCRIPTION_MIN_LENGTH,
            max: self::SENSOR_TYPE_DESCRIPTION_MAX_LENGTH,
            minMessage: "Sensor name must be at least {{ limit }} characters long",
            maxMessage: "Sensor name cannot be longer than {{ limit }} characters"
        ),
        Assert\NotBlank,
    ]
    private string $description;

    public function getSensorTypeID(): int
    {
        return $this->sensorTypeID;
    }

    public function setSensorTypeID(int $sensorTypeID): void
    {
        $this->sensorTypeID = $sensorTypeID;
    }

    public function getSensorType(): string
    {
        return $this->sensorType;
    }

    public function setSensorType(string $sensorType): void
    {
        $this->sensorType = $sensorType;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
}
