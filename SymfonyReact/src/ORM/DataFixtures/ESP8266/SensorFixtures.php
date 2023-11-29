<?php

namespace App\ORM\DataFixtures\ESP8266;

use App\ORM\DataFixtures\Core\UserDataFixtures;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\BoolReadingSensorInterface;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Motion;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Relay;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\StandardReadingSensorInterface;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Temperature;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\GenericMotion;
use App\Sensors\Entity\SensorTypes\GenericRelay;
use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\AnalogReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\HumidityReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\LatitudeReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\MotionSensorReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\RelayReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\SensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\TemperatureReadingTypeInterface;
use App\Sensors\Entity\SensorTypes\LDR;
use App\Sensors\Entity\SensorTypes\Sht;
use App\Sensors\Entity\SensorTypes\Soil;
use App\Sensors\Entity\SensorTypes\StandardSensorTypeInterface;
use DateInterval;
use DateTime;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;

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

        GenericRelay::NAME => [
            'alias' => GenericRelay::NAME,
            'object' => GenericRelay::class,
            'readingTypes' => [
                Relay::READING_TYPE =>  Relay::class,
            ],
        ],

        GenericMotion::NAME => [
            'alias' => GenericMotion::NAME,
            'object' => GenericMotion::class,
            'readingTypes' => [
                Motion::READING_TYPE =>  Motion::class,
            ],
        ],

        LDR::NAME => [
            'alias' => LDR::NAME,
            'object' => LDR::class,
            'readingTypes' => [
                Analog::READING_TYPE =>  Analog::class,
            ],
        ],

        Sht::NAME => [
            'alias' => Sht::NAME,
            'object' => Sht::class,
            'readingTypes' => [
                Temperature::READING_TYPE =>  Temperature::class,
                Humidity::READING_TYPE => Humidity::class,
            ],
        ],
    ];

    public const RELAY_SENSOR_NAME = 'AdDev1Relay';

    public const DHT_SENSOR_NAME = 'AdminDevice1Dht';

    public const DALLAS_SENSOR_NAME = 'AdminDevice1Dallas';

    public const BMP_SENSOR_NAME = 'AdDev1Bmp280';

    public const SOIL_SENSOR_NAME = 'AdminDev1Soil';

    public const MOTION_SENSOR_NAME = 'AdDev1Motion';

    /** one for each of the permission check devices */
    public const PERMISSION_CHECK_SENSORS = [
        'AdminUserOneDeviceAdminGroupOneDht' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::ADMIN_USER_ONE_DEVICE_ADMIN_GROUP_ONE],
            'sensorName' => self::DHT_SENSOR_NAME,
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Dht::NAME],
            'pinNumber' => 1,
        ],
        'AdminUserOneDeviceRegularGroupTwoDht' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::ADMIN_USER_ONE_DEVICE_REGULAR_GROUP_TWO],
            'sensorName' => 'AdminDevice2Dht',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Dht::NAME],
            'pinNumber' => 1,
        ],
        'AdminUserTwoDeviceAdminGroupTwoDht' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::ADMIN_USER_TWO_DEVICE_ADMIN_GROUP_TWO],
            'sensorName' => 'AdminDevice3Dht',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Dht::NAME],
            'pinNumber' => 1,
        ],
        'RegularUserOneDeviceRegularGroupOneDht' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::REGULAR_USER_ONE_DEVICE_REGULAR_GROUP_ONE],
            'sensorName' => 'UserDevice1Dht',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Dht::NAME],
            'pinNumber' => 1,
        ],
        'RegularUserTwoDeviceRegularGroupTwoDht' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::REGULAR_USER_TWO_DEVICE_REGULAR_GROUP_TWO],
            'sensorName' => 'UserDevice2Dht',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Dht::NAME],
            'pinNumber' => 1,
        ],
        'RegularUserTwoDeviceAdminGroupOneDht' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::REGULAR_USER_TWO_DEVICE_ADMIN_GROUP_ONE],
            'sensorName' => 'UserDevice3Dht',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Dht::NAME],
            'pinNumber' => 1,
        ],

        'AdminUserOneDeviceAdminGroupOneDallas' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::ADMIN_USER_ONE_DEVICE_ADMIN_GROUP_ONE],
            'sensorName' => self::DALLAS_SENSOR_NAME,
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Dallas::NAME],
            'pinNumber' => 2,
        ],

        'AdminUserOneDeviceRegularGroupTwoDallas' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::ADMIN_USER_ONE_DEVICE_REGULAR_GROUP_TWO],
            'sensorName' => 'AdminDevice2Dallas',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Dallas::NAME],
            'pinNumber' => 2,
        ],

        'AdminUserTwoDeviceAdminGroupTwoDallas' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::ADMIN_USER_TWO_DEVICE_ADMIN_GROUP_TWO],
            'sensorName' => 'AdminDevice3Dallas',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Dallas::NAME],
            'pinNumber' => 2,
        ],

        'RegularUserOneDeviceRegularGroupOneDallas' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::REGULAR_USER_ONE_DEVICE_REGULAR_GROUP_ONE],
            'sensorName' => 'UserDev1DS180',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Dallas::NAME],
            'pinNumber' => 2,
        ],

        'RegularUserTwoDeviceRegularGroupTwoDallas' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::REGULAR_USER_TWO_DEVICE_REGULAR_GROUP_TWO],
            'sensorName' => 'UserDev2DS180',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Dallas::NAME],
            'pinNumber' => 2,
        ],

        'RegularUserTwoDeviceAdminGroupOneDallas' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::REGULAR_USER_TWO_DEVICE_ADMIN_GROUP_ONE],
            'sensorName' => 'UserDev3DS180',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Dallas::NAME],
            'pinNumber' => 2,
        ],

        'AdminUserOneDeviceAdminGroupOneSoil' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::ADMIN_USER_ONE_DEVICE_ADMIN_GROUP_ONE],
            'sensorName' => self::SOIL_SENSOR_NAME,
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Soil::NAME],
            'pinNumber' => 3,
        ],

        'AdminUserOneDeviceRegularGroupTwoSoil' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::ADMIN_USER_ONE_DEVICE_REGULAR_GROUP_TWO],
            'sensorName' => 'AdminDev2Soil',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Soil::NAME],
            'pinNumber' => 3,
        ],

        'AdminUserTwoDeviceAdminGroupTwoSoil' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::ADMIN_USER_TWO_DEVICE_ADMIN_GROUP_TWO],
            'sensorName' => 'AdminDev3Soil',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Soil::NAME],
            'pinNumber' => 3,
        ],

        'RegularUserOneDeviceRegularGroupOneSoil' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::REGULAR_USER_ONE_DEVICE_REGULAR_GROUP_ONE],
            'sensorName' => 'UsrDev1Soil',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Soil::NAME],
            'pinNumber' => 3,
        ],

        'RegularUserTwoDeviceRegularGroupTwoSoil' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::REGULAR_USER_TWO_DEVICE_REGULAR_GROUP_TWO],
            'sensorName' => 'UsrDev2Soil',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Soil::NAME],
            'pinNumber' => 3,
        ],

        'RegularUserTwoDeviceAdminGroupOneSoil' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::REGULAR_USER_TWO_DEVICE_ADMIN_GROUP_ONE],
            'sensorName' => 'UsrDev3Soil',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Soil::NAME],
            'pinNumber' => 3,
        ],

        'AdminUserOneDeviceAdminGroupOneBmp' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::ADMIN_USER_ONE_DEVICE_ADMIN_GROUP_ONE],
            'sensorName' => self::BMP_SENSOR_NAME,
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Bmp::NAME],
            'pinNumber' => 4,
        ],

        'AdminUserOneDeviceRegularGroupTwoBmp' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::ADMIN_USER_ONE_DEVICE_REGULAR_GROUP_TWO],
            'sensorName' => 'AdDev2Bmp280',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Bmp::NAME],
            'pinNumber' => 4,
        ],

        'AdminUserTwoDeviceAdminGroupTwoBmp' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::ADMIN_USER_TWO_DEVICE_ADMIN_GROUP_TWO],
            'sensorName' => 'AdDev3Bmp280',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Bmp::NAME],
            'pinNumber' => 4,
        ],

        'RegularUserOneDeviceRegularGroupOneBmp' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::REGULAR_USER_ONE_DEVICE_REGULAR_GROUP_ONE],
            'sensorName' => 'UsDev1Bmp280',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Bmp::NAME],
            'pinNumber' => 4,
        ],

        'RegularUserTwoDeviceRegularGroupTwoBmp' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::REGULAR_USER_TWO_DEVICE_REGULAR_GROUP_TWO],
            'sensorName' => 'UsDev2Bmp280',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Bmp::NAME],
            'pinNumber' => 4,
        ],

        'RegularUserTwoDeviceAdminGroupOneBmp' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::REGULAR_USER_TWO_DEVICE_ADMIN_GROUP_ONE],
            'sensorName' => 'UsDev3Bmp280',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Bmp::NAME],
            'pinNumber' => 4,
        ],

        'AdminUserOneDeviceAdminGroupOneRelay' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::ADMIN_USER_ONE_DEVICE_ADMIN_GROUP_ONE],
            'sensorName' => self::RELAY_SENSOR_NAME,
            'sensors' => self::ALL_SENSOR_TYPE_DATA[GenericRelay::NAME],
            'pinNumber' => 5,
        ],

        'AdminUserOneDeviceRegularGroupTwoRelay' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::ADMIN_USER_ONE_DEVICE_REGULAR_GROUP_TWO],
            'sensorName' => 'AdDev2Relay',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[GenericRelay::NAME],
            'pinNumber' => 5,
        ],

        'AdminUserTwoDeviceAdminGroupTwoRelay' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::ADMIN_USER_TWO_DEVICE_ADMIN_GROUP_TWO],
            'sensorName' => 'AdDev3Relay',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[GenericRelay::NAME],
            'pinNumber' => 5,
        ],

        'RegularUserOneDeviceRegularGroupOneRelay' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::REGULAR_USER_ONE_DEVICE_REGULAR_GROUP_ONE],
            'sensorName' => 'UsDev1Relay',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[GenericRelay::NAME],
            'pinNumber' => 5,
        ],

        'RegularUserTwoDeviceRegularGroupTwoRelay' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::REGULAR_USER_TWO_DEVICE_REGULAR_GROUP_TWO],
            'sensorName' => 'UsDev2Relay',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[GenericRelay::NAME],
            'pinNumber' => 5,
        ],

        'RegularUserTwoDeviceAdminGroupOneRelay' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::REGULAR_USER_TWO_DEVICE_ADMIN_GROUP_ONE],
            'sensorName' => 'UsDev3Relay',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[GenericRelay::NAME],
            'pinNumber' => 5,
        ],

        'AdminUserOneDeviceAdminGroupOneMotion' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::ADMIN_USER_ONE_DEVICE_ADMIN_GROUP_ONE],
            'sensorName' => self::MOTION_SENSOR_NAME,
            'sensors' => self::ALL_SENSOR_TYPE_DATA[GenericMotion::NAME],
            'pinNumber' => 6,
        ],

        'AdminUserOneDeviceRegularGroupTwoMotion' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::ADMIN_USER_ONE_DEVICE_REGULAR_GROUP_TWO],
            'sensorName' => 'AdDev2Motion',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[GenericMotion::NAME],
            'pinNumber' => 6,
        ],

        'AdminUserTwoDeviceAdminGroupOneMotion' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::ADMIN_USER_TWO_DEVICE_ADMIN_GROUP_ONE],
            'sensorName' => 'AdDev3Motion',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[GenericMotion::NAME],
            'pinNumber' => 6,
        ],

        'AdminUserTwoDeviceAdminGroupTwoMotion' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::ADMIN_USER_TWO_DEVICE_ADMIN_GROUP_TWO],
            'sensorName' => 'AdDev4Motion',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[GenericMotion::NAME],
            'pinNumber' => 6,
        ],

        'RegularUserOneDeviceRegularGroupOneMotion' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::REGULAR_USER_ONE_DEVICE_REGULAR_GROUP_ONE],
            'sensorName' => 'UsDev1Motion',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[GenericMotion::NAME],
            'pinNumber' => 6,
        ],

        'RegularUserTwoDeviceAdminGroupOneMotion' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::REGULAR_USER_TWO_DEVICE_ADMIN_GROUP_ONE],
            'sensorName' => 'UsDev3Motion',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[GenericMotion::NAME],
            'pinNumber' => 6,
        ],

        'RegularUserTwoDeviceRegularGroupTwoMotion' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::REGULAR_USER_TWO_DEVICE_REGULAR_GROUP_TWO],
            'sensorName' => 'UsDev2Motion',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[GenericMotion::NAME],
            'pinNumber' => 6,
        ],

        //LDR
        'AdminUserOneDeviceAdminGroupOneLDR' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::ADMIN_USER_ONE_DEVICE_ADMIN_GROUP_ONE],
            'sensorName' => 'AdDev1LDR',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[LDR::NAME],
            'pinNumber' => 7,
        ],

        'AdminUserOneDeviceRegularGroupTwoLDR' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::ADMIN_USER_ONE_DEVICE_REGULAR_GROUP_TWO],
            'sensorName' => 'AdDev2LDR',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[LDR::NAME],
            'pinNumber' => 7,
        ],

        'AdminUserTwoDeviceAdminGroupTwoLDR' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::ADMIN_USER_TWO_DEVICE_ADMIN_GROUP_TWO],
            'sensorName' => 'AdDev3LDR',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[LDR::NAME],
            'pinNumber' => 7,
        ],

        'RegularUserOneDeviceRegularGroupOneLDR' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::REGULAR_USER_ONE_DEVICE_REGULAR_GROUP_ONE],
            'sensorName' => 'UsDev1LDR',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[LDR::NAME],
            'pinNumber' => 7,
        ],

        'RegularUserTwoDeviceRegularGroupTwoLDR' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::REGULAR_USER_TWO_DEVICE_REGULAR_GROUP_TWO],
            'sensorName' => 'UsDev2LDR',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[LDR::NAME],
            'pinNumber' => 7,
        ],

        'RegularUserTwoDeviceAdminGroupOneLDR' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::REGULAR_USER_TWO_DEVICE_ADMIN_GROUP_ONE],
            'sensorName' => 'UsDev3LDR',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[LDR::NAME],
            'pinNumber' => 7,
        ],

        //SHT
        'AdminUserOneDeviceAdminGroupOneSHT' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::ADMIN_USER_ONE_DEVICE_ADMIN_GROUP_ONE],
            'sensorName' => 'AdDev1SHT',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Sht::NAME],
            'pinNumber' => 8,
        ],

        'AdminUserOneDeviceRegularGroupTwoSHT' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::ADMIN_USER_ONE_DEVICE_REGULAR_GROUP_TWO],
            'sensorName' => 'AdDev2SHT',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Sht::NAME],
            'pinNumber' => 8,
        ],

        'AdminUserTwoDeviceAdminGroupTwoSHT' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::ADMIN_USER_TWO_DEVICE_ADMIN_GROUP_TWO],
            'sensorName' => 'AdDev3SHT',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Sht::NAME],
            'pinNumber' => 8,
        ],

        'RegularUserOneDeviceRegularGroupOneSHT' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::REGULAR_USER_ONE_DEVICE_REGULAR_GROUP_ONE],
            'sensorName' => 'UsDev1SHT',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Sht::NAME],
            'pinNumber' => 8,
        ],

        'RegularUserTwoDeviceRegularGroupTwoSHT' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::REGULAR_USER_TWO_DEVICE_REGULAR_GROUP_TWO],
            'sensorName' => 'UsDev2SHT',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Sht::NAME],
            'pinNumber' => 8,
        ],

        'RegularUserTwoDeviceAdminGroupOneSHT' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::REGULAR_USER_TWO_DEVICE_ADMIN_GROUP_ONE],
            'sensorName' => 'UsDev3SHT',
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Sht::NAME],
            'pinNumber' => 8,
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
        'AdminUserOneDeviceAdminGroupOneRelay',
        'AdminUserOneDeviceRegularGroupTwoRelay',
        'AdminUserOneDeviceAdminGroupOneMotion',
        'AdminUserOneDeviceRegularGroupTwoMotion',
        'AdminUserOneDeviceAdminGroupOneLDR',
        'AdminUserOneDeviceRegularGroupTwoLDR',
        'AdminUserOneDeviceAdminGroupOneSHT',
        'AdminUserOneDeviceRegularGroupTwoSHT',
    ];

    public const ADMIN_USER_TWO_OWNED_SENSORS = [
        'AdminUserTwoDeviceAdminGroupTwoDht',
        'AdminUserTwoDeviceAdminGroupTwoDallas',
        'AdminUserTwoDeviceAdminGroupTwoSoil',
        'AdminUserTwoDeviceAdminGroupTwoBmp',
        'AdminUserTwoDeviceAdminGroupTwoRelay',
        'AdminUserTwoDeviceAdminGroupTwoMotion',
        'AdminUserTwoDeviceAdminGroupTwoLDR',
        'AdminUserTwoDeviceAdminGroupTwoSHT',
    ];

    public const REGULAR_USER_ONE_OWNED_SENSORS = [
        'RegularUserOneDeviceRegularGroupOneDht',
        'RegularUserOneDeviceRegularGroupOneDallas',
        'RegularUserOneDeviceRegularGroupOneSoil',
        'RegularUserOneDeviceRegularGroupOneBmp',
        'RegularUserOneDeviceRegularGroupOneRelay',
        'RegularUserOneDeviceRegularGroupOneMotion',
        'RegularUserOneDeviceRegularGroupOneLDR',
        'RegularUserOneDeviceRegularGroupOneSHT',
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
        'RegularUserTwoDeviceRegularGroupTwoRelay',
        'RegularUserTwoDeviceRegularGroupTwoMotion',
        'RegularUserTwoDeviceRegularGroupTwoLDR',
        'RegularUserTwoDeviceRegularGroupTwoSHT',
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
        'AdminUserOneDeviceAdminGroupOneRelay',
        'RegularUserTwoDeviceAdminGroupOneRelay',
        'AdminUserOneDeviceAdminGroupOneMotion',
        'RegularUserTwoDeviceAdminGroupOneMotion',
        'AdminUserTwoDeviceAdminGroupOneMotion',
        'RegularUserTwoDeviceAdminGroupOneLDR',
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
        'AdminUserOneDeviceRegularGroupTwoRelay',
        'RegularUserTwoDeviceRegularGroupTwoRelay',
        'AdminUserOneDeviceRegularGroupTwoMotion',
        'RegularUserTwoDeviceRegularGroupTwoMotion',
        'AdminUserTwoDeviceAdminGroupTwoMotion',
        'RegularUserTwoDeviceRegularGroupTwoLDR',
        'RegularUserTwoDeviceRegularGroupTwoSHT',
    ];

    public const SENSORS = [
        Dht::NAME => 'AdminDHTSensor',
        Dallas::NAME => 'AdminDallasSensor',
        Soil::NAME => 'AdminSoilSensor',
        Bmp::NAME => 'AdminBmpSensor',
        GenericRelay::NAME => 'AdminRelaySensor',
        GenericMotion::NAME => 'AdminMotionSensor',
        LDR::NAME => 'AdminLDRSensor',
        Sht::NAME => 'AdminSHTSensor',
    ];

    public function getOrder(): int
    {
        return self::FIXTURE_ORDER;
    }

    public function load(ObjectManager $manager): void
    {
        foreach (self::PERMISSION_CHECK_SENSORS as $sensorDetails) {
            $minueteInterval = random_int(0, 59);
            $createdAt = (new DateTime('now'))->add(new DateInterval('PT' . $minueteInterval . 'M'));
                $sensor = new Sensor();
            $sensor->setDevice($this->getReference($sensorDetails['device']['referenceName']));
            $sensor->setSensorName($sensorDetails['sensorName']);
            $sensor->setSensorTypeID($this->getReference($sensorDetails['sensors']['alias']));
            $sensor->setCreatedBy($this->getReference(UserDataFixtures::ADMIN_USER_EMAIL_ONE));
            $sensor->setPinNumber($sensorDetails['pinNumber']);
            $sensor->setCreatedAt($createdAt);
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

        try {
            $manager->flush();
        } catch (Exception $e) {
            dd($e);
        }
    }

    /**
     * @throws Exception
     */
    private function setSensorObjects(
        string $readingTypeObjects,
        Sensor $newSensor,
        SensorTypeInterface $newSensorType,
        ObjectManager $manager
    ): void {
        $newObject = new $readingTypeObjects();

        if ($newObject instanceof AllSensorReadingTypeInterface) {
            $newObject->setSensor($newSensor);
            $newSensorType->setSensor($newSensor);
            $newObject->setUpdatedAt();
            $newObject->setCreatedAt(new DateTimeImmutable('now'));
        }

        if ($newObject instanceof StandardReadingSensorInterface) {
            if ($newSensorType instanceof StandardSensorTypeInterface) {
                if ($newSensorType instanceof TemperatureReadingTypeInterface && $newObject instanceof Temperature) {
                    $newObject->setCurrentReading($newSensorType->getMinTemperature());
                    $newSensorType->setTemperature($newObject);
                }
                if ($newSensorType instanceof HumidityReadingTypeInterface && $newObject instanceof Humidity) {
                    $newObject->setCurrentReading($newSensorType->getMinHumidity());
                    $newSensorType->setHumidObject($newObject);
                }
                if ($newSensorType instanceof LatitudeReadingTypeInterface && $newObject instanceof Latitude) {
                    $newObject->setCurrentReading($newSensorType->getMinLatitude());
                    $newSensorType->setLatitudeObject($newObject);
                }
                if ($newSensorType instanceof AnalogReadingTypeInterface && $newObject instanceof Analog) {
                    $newObject->setCurrentReading($newSensorType->getMinAnalog());
                    $newObject->setLowReading($newSensorType->getMinAnalog());
                    $newObject->setHighReading($newSensorType->getMaxAnalog());
                    $newSensorType->setAnalogObject($newObject);
                }
            }
        } elseif ($newObject instanceof BoolReadingSensorInterface) {
            $newObject->setCurrentReading(true);
            $newObject->setExpectedReading(true);
            $newObject->setCurrentReading(true);
            $newObject->setRequestedReading(true);
            $newObject->setCreatedAt(new DateTimeImmutable('now'));
            $newObject->setUpdatedAt();
            if ($newSensorType instanceof MotionSensorReadingTypeInterface) {
                $newSensorType->setMotion($newObject);
                $manager->persist($newSensorType);
            }
            if ($newSensorType instanceof RelayReadingTypeInterface) {
                $newSensorType->setRelay($newObject);
            }
        } else {
            throw new Exception('Sensor type not found');
        }
        $manager->persist($newSensorType);
        $manager->persist($newObject);
    }
}
