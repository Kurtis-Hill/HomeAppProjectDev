<?php

namespace App\ORM\DataFixtures\ESP8266;

use App\ORM\DataFixtures\Core\UserDataFixtures;
use App\Sensors\Entity\ReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;
use App\Sensors\Entity\ReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\Temperature;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\Interfaces\AnalogSensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\HumiditySensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\LatitudeSensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\StandardSensorReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\TemperatureSensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Soil;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SensorFixtures extends Fixture implements OrderedFixtureInterface
{
    private const FIXTURE_ORDER = 8;

    public const ALL_SENSOR_TYPE_DATA = [
        Dht::NAME => [
            'alias' => Dht::NAME,
            'object' => Dht::class,
            'readingTypes' => [
                Temperature::READING_TYPE =>  Temperature::class,
                Humidity::READING_TYPE => Humidity::class,
            ],
        ],

        Dallas::NAME => [
            'alias' => Dallas::NAME,
            'object' => Dallas::class,
            'readingTypes' => [
                Temperature::READING_TYPE =>  Temperature::class,
            ],
        ],

        Soil::NAME => [
            'alias' => Soil::NAME,
            'object' => Soil::class,
            'readingTypes' => [
                Analog::READING_TYPE =>  Analog::class,
            ],
        ],

        Bmp::NAME => [
            'alias' => Bmp::NAME,
            'object' => Bmp::class,
            'readingTypes' => [
                Temperature::READING_TYPE =>  Temperature::class,
                Humidity::READING_TYPE => Humidity::class,
                Latitude::READING_TYPE => Latitude::class,
            ],
        ],
    ];

    /** one for each of the permission check devices */
    public const PERMISSION_CHECK_SENSORS = [
        'AdminUserOneDeviceAdminGroupOneDht' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES['AdminUserOneDeviceAdminGroupOne'],
            'sensorName' => 'AdminDevice1Dht',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Dht::NAME]
        ],
        'AdminUserOneDeviceRegularGroupTwoDht' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES['AdminUserOneDeviceRegularGroupTwo'],
            'sensorName' => 'AdminDevice2Dht',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Dht::NAME]
        ],
        'AdminUserTwoDeviceAdminGroupTwoDht' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES['AdminUserTwoDeviceAdminGroupTwo'],
            'sensorName' => 'AdminDevice3Dht',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Dht::NAME]
        ],
        'RegularUserOneDeviceRegularGroupOneDht' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES['RegularUserOneDeviceRegularGroupOne'],
            'sensorName' => 'UserDevice1Dht',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Dht::NAME]
        ],
        'RegularUserTwoDeviceRegularGroupTwoDht' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES['RegularUserTwoDeviceRegularGroupTwo'],
            'sensorName' => 'UserDevice2Dht',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Dht::NAME]
        ],
        'RegularUserTwoDeviceAdminGroupOneDht' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES['RegularUserTwoDeviceAdminGroupOne'],
            'sensorName' => 'UserDevice3Dht',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Dht::NAME]
        ],

        'AdminUserOneDeviceAdminGroupOneDallas' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES['AdminUserOneDeviceAdminGroupOne'],
            'sensorName' => 'AdminDevice1Dallas',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Dallas::NAME]
        ],

        'AdminUserOneDeviceRegularGroupTwoDallas' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES['AdminUserOneDeviceRegularGroupTwo'],
            'sensorName' => 'AdminDevice2Dallas',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Dallas::NAME]
        ],

        'AdminUserTwoDeviceAdminGroupTwoDallas' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES['AdminUserTwoDeviceAdminGroupTwo'],
            'sensorName' => 'AdminDevice3Dallas',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Dallas::NAME]
        ],

        'RegularUserOneDeviceRegularGroupOneDallas' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES['RegularUserOneDeviceRegularGroupOne'],
            'sensorName' => 'UserDev1DS180',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Dallas::NAME]
        ],

        'RegularUserTwoDeviceRegularGroupTwoDallas' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES['RegularUserTwoDeviceRegularGroupTwo'],
            'sensorName' => 'UserDev2DS180',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Dallas::NAME]
        ],

        'RegularUserTwoDeviceAdminGroupOneDallas' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES['RegularUserTwoDeviceAdminGroupOne'],
            'sensorName' => 'UserDev3DS180',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Dallas::NAME]
        ],

        'AdminUserOneDeviceAdminGroupOneSoil' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES['AdminUserOneDeviceAdminGroupOne'],
            'sensorName' => 'AdminDev1Soil',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Soil::NAME]
        ],

        'AdminUserOneDeviceRegularGroupTwoSoil' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES['AdminUserOneDeviceRegularGroupTwo'],
            'sensorName' => 'AdminDev2Soil',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Soil::NAME]
        ],

        'AdminUserTwoDeviceAdminGroupTwoSoil' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES['AdminUserTwoDeviceAdminGroupTwo'],
            'sensorName' => 'AdminDev3Soil',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Soil::NAME]
        ],

        'RegularUserOneDeviceRegularGroupOneSoil' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES['RegularUserOneDeviceRegularGroupOne'],
            'sensorName' => 'UsrDev1Soil',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Soil::NAME]
        ],

        'RegularUserTwoDeviceRegularGroupTwoSoil' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES['RegularUserTwoDeviceRegularGroupTwo'],
            'sensorName' => 'UsrDev2Soil',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Soil::NAME]
        ],

        'RegularUserTwoDeviceAdminGroupOneSoil' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES['RegularUserTwoDeviceAdminGroupOne'],
            'sensorName' => 'UsrDev3Soil',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Soil::NAME]
        ],

        'AdminUserOneDeviceAdminGroupOneBmp' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES['AdminUserOneDeviceAdminGroupOne'],
            'sensorName' => 'AdDev1Bmp280',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Bmp::NAME]
        ],

        'AdminUserOneDeviceRegularGroupTwoBmp' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES['AdminUserOneDeviceRegularGroupTwo'],
            'sensorName' => 'AdDev2Bmp280',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Bmp::NAME]
        ],

        'AdminUserTwoDeviceAdminGroupTwoBmp' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES['AdminUserTwoDeviceAdminGroupTwo'],
            'sensorName' => 'AdDev3Bmp280',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Bmp::NAME]
        ],

        'RegularUserOneDeviceRegularGroupOneBmp' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES['RegularUserOneDeviceRegularGroupOne'],
            'sensorName' => 'UsDev1Bmp280',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Bmp::NAME]
        ],

        'RegularUserTwoDeviceRegularGroupTwoBmp' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES['RegularUserTwoDeviceRegularGroupTwo'],
            'sensorName' => 'UsDev2Bmp280',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Bmp::NAME]
        ],

        'RegularUserTwoDeviceAdminGroupOneBmp' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES['RegularUserTwoDeviceAdminGroupOne'],
            'sensorName' => 'UsDev3Bmp280',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Bmp::NAME]
        ],
    ];

    public const ADMIN_USER_ONE_OWNED_SENSORS = [
        'AdminUserOneDeviceAdminGroupOneDht',
        'AdminUserOneDeviceRegularGroupTwoDht',
        'AdminUserOneDeviceAdminGroupOneDallas',
        'AdminUserOneDeviceRegularGroupTwoDallas',
        'AdminUserOneDeviceAdminGroupOneSoil',
        'AdminUserOneDeviceRegularGroupTwoSoil',
        'AdminUserOneDeviceAdminGroupOneBmp',
        'AdminUserOneDeviceRegularGroupTwoBmp',
    ];

    public const ADMIN_USER_TWO_OWNED_SENSORS = [
        'AdminUserTwoDeviceAdminGroupTwoDht',
        'AdminUserTwoDeviceAdminGroupTwoDallas',
        'AdminUserTwoDeviceAdminGroupTwoSoil',
        'AdminUserTwoDeviceAdminGroupTwoBmp',
    ];

    public const REGULAR_USER_ONE_OWNED_SENSORS = [
        'RegularUserOneDeviceRegularGroupOneDht',
        'RegularUserOneDeviceRegularGroupOneDallas',
        'RegularUserOneDeviceRegularGroupOneSoil',
        'RegularUserOneDeviceRegularGroupOneBmp',
    ];

    public const REGULAR_USER_TWO_OWNED_SENSORS = [
        'RegularUserTwoDeviceRegularGroupTwoDht',
        'RegularUserTwoDeviceRegularGroupTwoDallas',
        'RegularUserTwoDeviceRegularGroupTwoSoil',
        'RegularUserTwoDeviceRegularGroupTwoBmp',
        'RegularUserTwoDeviceAdminGroupOneDht',
        'RegularUserTwoDeviceAdminGroupOneDallas',
        'RegularUserTwoDeviceAdminGroupOneSoil',
        'RegularUserTwoDeviceAdminGroupOneBmp',
    ];

    public const GROUP_ONE_SENSORS = [
        'AdminUserOneDeviceAdminGroupOneDht',
        'AdminUserOneDeviceAdminGroupOneDallas',
        'AdminUserOneDeviceAdminGroupOneSoil',
        'AdminUserOneDeviceAdminGroupOneBmp',
        'RegularUserTwoDeviceAdminGroupOneDht',
        'RegularUserTwoDeviceAdminGroupOneDallas',
        'RegularUserTwoDeviceAdminGroupOneSoil',
        'RegularUserTwoDeviceAdminGroupOneBmp',
    ];

    public const GROUP_TWO_SENSORS = [
        'AdminUserOneDeviceRegularGroupTwoDht',
        'AdminUserOneDeviceRegularGroupTwoDallas',
        'AdminUserOneDeviceRegularGroupTwoSoil',
        'AdminUserOneDeviceRegularGroupTwoBmp',
        'AdminUserTwoDeviceAdminGroupTwoDht',
        'AdminUserTwoDeviceAdminGroupTwoDallas',
        'AdminUserTwoDeviceAdminGroupTwoSoil',
        'AdminUserTwoDeviceAdminGroupTwoBmp',
        'RegularUserTwoDeviceRegularGroupTwoDht',
        'RegularUserTwoDeviceRegularGroupTwoDallas',
        'RegularUserTwoDeviceRegularGroupTwoSoil',
        'RegularUserTwoDeviceRegularGroupTwoBmp',
    ];

    public const SENSORS = [
        Dht::NAME => 'AdminDHTSensor',
        Dallas::NAME => 'AdminDallasSensor',
        Soil::NAME => 'AdminSoilSensor',
        Bmp::NAME => 'AdminBmpSensor'
    ];

    public function getOrder(): int
    {
        return self::FIXTURE_ORDER;
    }

    public function load(ObjectManager $manager): void
    {
        foreach (self::PERMISSION_CHECK_SENSORS as $sensorKey => $sensorDetails) {
            $sensor = new Sensor();
            $sensor->setDevice($this->getReference($sensorDetails['device']['referenceName']));
            $sensor->setSensorName($sensorDetails['sensorName']);
            $sensor->setSensorTypeID($this->getReference($sensorDetails['sensors']['alias']));
            $sensor->setCreatedBy($this->getReference(UserDataFixtures::ADMIN_USER_EMAIL_ONE));

            $this->addReference($sensorDetails['sensorName'], $sensor);
            $manager->persist($sensor);

            $newSensorType = new $sensorDetails['sensors']['object']();

            foreach ($sensorDetails['sensors']['readingTypes'] as $readingType) {
                $this->setSensorObjects(
                    $readingType,
                    $sensor,
                    $newSensorType,
                    $manager
                );
            }
        }

        $manager->flush();
    }

    private function setSensorObjects(
        string $readingTypeObjects,
        Sensor $newSensor,
        SensorTypeInterface $newSensorType,
        ObjectManager $manager
    ): void {
        $newObject = new $readingTypeObjects();
        if ($newObject instanceof StandardReadingSensorInterface) {
            $newObject->setSensor($newSensor);
            $newObject->setUpdatedAt();

            if ($newSensorType instanceof StandardSensorReadingTypeInterface) {
                $newSensorType->setSensor($newSensor);
                if ($newSensorType instanceof TemperatureSensorTypeInterface && $newObject instanceof Temperature) {
                    $newObject->setCurrentReading(10);
                    $newSensorType->setTemperature($newObject);
                }
                if ($newSensorType instanceof HumiditySensorTypeInterface && $newObject instanceof Humidity) {
                    $newObject->setCurrentReading(10);
                    $newSensorType->setHumidObject($newObject);
                }
                if ($newSensorType instanceof LatitudeSensorTypeInterface && $newObject instanceof Latitude) {
                    $newObject->setCurrentReading(10);
                    $newSensorType->setLatitudeObject($newObject);
                }
                if ($newSensorType instanceof AnalogSensorTypeInterface && $newObject instanceof Analog) {
                    $newObject->setCurrentReading(1001);
                    $newSensorType->setAnalogObject($newObject);
                }
            }
            $manager->persist($newSensorType);
            $manager->persist($newObject);
        }
    }
}
