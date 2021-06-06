<?php

namespace App\Entity\Sensors;

use App\Entity\Sensors\ReadingTypes\Analog;
use App\Entity\Sensors\ReadingTypes\Humidity;
use App\Entity\Sensors\ReadingTypes\Latitude;
use App\Entity\Sensors\ReadingTypes\Temperature;
use App\Entity\Sensors\SensorTypes\Bmp;
use App\Entity\Sensors\SensorTypes\Dallas;
use App\Entity\Sensors\SensorTypes\Dht;
use App\Entity\Sensors\SensorTypes\Soil;
use App\Form\CardViewForms\StandardSensorOutOFBoundsForm;
use App\Form\SensorForms\UpdateReadingForm;
use Doctrine\ORM\Mapping as ORM;

/**
 * SensorTypes
 *
 * @ORM\Table(name="sensortype", uniqueConstraints={@ORM\UniqueConstraint(name="sensorType", columns={"sensorType"})})
 * @ORM\Entity(repositoryClass="App\Repository\Core\SensorTypeRepository")
 */
class SensorType
{
    public const OUT_OF_BOUND_FORM_ARRAY_KEY = 'outOfBounds';

    public const UPDATE_CURRENT_READING_FORM_ARRAY_KEY = 'updateCurrentReading';

    public const DHT_SENSOR = 'Dht';

    public const BMP_SENSOR = 'Bmp';

    public const DALLAS_TEMPERATURE = 'Dallas';

    public const SOIL_SENSOR = 'Soil';

    public const SENSOR_TYPES = [
        self::DHT_SENSOR,
        self::BMP_SENSOR,
        self::DALLAS_TEMPERATURE,
        self::SOIL_SENSOR
    ];

    public const SENSOR_TYPE_DATA = [
        SensorType::DHT_SENSOR => [
            'alias' => 'dht',
            'object' => Dht::class,
            'readingTypes' => [
                'temperature' =>  Temperature::class,
                'humidity' => Humidity::class,
            ],
            'forms' => [
                'outOfBounds' => [
                    'form' => StandardSensorOutOFBoundsForm::class,
                    'readingTypes' => [
                        'temperature' =>  Temperature::class,
                        'humidity' => Humidity::class,
                    ],
                ],
                'updateCurrentReading' => [
                    'form' => UpdateReadingForm::class,
                    'readingTypes' => [
                        'temperature' =>  Temperature::class,
                        'humidity' => Humidity::class,
                    ],
                ]
            ]
        ],

        SensorType::DALLAS_TEMPERATURE => [
            'alias' => 'dallas',
            'object' => Dallas::class,
            'readingTypes' => [
                'temperature' =>  Temperature::class,
            ],
            'forms' => [
                'outOfBounds' => [
                    'form' => StandardSensorOutOFBoundsForm::class,
                    'readingTypes' => [
                        'temperature' =>  Temperature::class,
                    ],
                ],
                'updateCurrentReading' => [
                    'form' => UpdateReadingForm::class,
                    'readingTypes' => [
                        'temperature' =>  Temperature::class,
                    ],
                ]
            ]
        ],

        SensorType::SOIL_SENSOR => [
            'alias' => 'soil',
            'object' => Soil::class,
            'readingTypes' => [
                'analog' =>  Analog::class,
            ],
            'forms' => [
                'outOfBounds' => [
                    'form' => StandardSensorOutOFBoundsForm::class,
                    'readingTypes' => [
                        'analog' =>  Analog::class,
                    ],
                ],
                'updateCurrentReading' => [
                    'form' => UpdateReadingForm::class,
                    'readingTypes' => [
                        'analog' =>  Analog::class,
                    ],
                ]
            ]
        ],

        SensorType::BMP_SENSOR => [
            'alias' => 'bmp',
            'object' => Bmp::class,
            'readingTypes' => [
                'temperature' =>  Temperature::class,
                'humidity' => Humidity::class,
                'latitude' => Latitude::class,
            ],
            'forms' => [
                'outOfBounds' => [
                    'form' => StandardSensorOutOFBoundsForm::class,
                    'readingTypes' => [
                        'temperature' =>  Temperature::class,
                        'humidity' =>  Humidity::class,
                        'latitude' => Latitude::class,
                    ],
                ],
                'updateCurrentReading' => [
                    'form' => UpdateReadingForm::class,
                    'readingTypes' => [
                        'temperature' =>  Temperature::class,
                        'humidity' =>  Humidity::class,
                        'latitude' => Latitude::class,
                    ],
                ]
            ]
        ],
    ];

    /**
     * @var int
     *
     * @ORM\Column(name="sensorTypeID", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $sensorTypeID;

    /**
     * @var string
     *
     * @ORM\Column(name="sensorType", type="string", length=20, nullable=false)
     */
    private string $sensorType;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=50, nullable=false)
     */
    private string $description;

    /**
     * @return int
     */
    public function getSensorTypeID(): int
    {
        return $this->sensorTypeID;
    }

    /**
     * @param int $sensorTypeID
     */
    public function setSensorTypeID(int $sensorTypeID): void
    {
        $this->sensorTypeID = $sensorTypeID;
    }

    /**
     * @return string
     */
    public function getSensorType(): string
    {
        return $this->sensorType;
    }

    /**
     * @param string $sensorType
     */
    public function setSensorType(string $sensorType): void
    {
        $this->sensorType = $sensorType;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }


}
