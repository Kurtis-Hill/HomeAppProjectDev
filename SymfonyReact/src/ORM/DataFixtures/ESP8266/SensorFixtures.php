<?php

namespace App\ORM\DataFixtures\ESP8266;

use App\ORM\DataFixtures\Core\UserDataFixtures;
use App\Sensors\Entity\ReadingTypes\BaseReadingTypeInterface;
use App\Sensors\Entity\ReadingTypes\BaseSensorReadingType;
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


    public const ADMIN_1_DHT_SENSOR_NAME = 'AdminDevice1Dht';

    public const ADMIN_2_DHT_SENSOR_NAME = 'AdminDevice2Dht';

    public const ADMIN_3_DHT_SENSOR_NAME = 'AdminDevice3Dht';

    public const REGULAR_1_DHT_SENSOR_NAME = 'UserDevice1Dht';

    public const REGULAR_2_DHT_SENSOR_NAME = 'UserDevice2Dht';

    public const REGULAR_3_DHT_SENSOR_NAME = 'UserDevice3Dht';


    public const ADMIN_1_DALLAS_SENSOR_NAME = 'AdminDevice1Dallas';

    public const ADMIN_2_DALLAS_SENSOR_NAME = 'AdminDevice2Dallas';

    public const ADMIN_3_DALLAS_SENSOR_NAME = 'AdminDevice3Dallas';

    public const REGULAR_1_DALLAS_SENSOR_NAME = 'UserDevice1Dallas';

    public const REGULAR_2_DALLAS_SENSOR_NAME = 'UserDevice2Dallas';

    public const REGULAR_3_DALLAS_SENSOR_NAME = 'UserDevice3Dallas';


    public const ADMIN_1_BMP_SENSOR_NAME = 'AdDev1Bmp280';

    public const ADMIN_2_BMP_SENSOR_NAME = 'AdDev2Bmp280';

    public const ADMIN_3_BMP_SENSOR_NAME = 'AdDev3Bmp280';

    public const REGULAR_1_BMP_SENSOR_NAME = 'UsDev1Bmp280';

    public const REGULAR_2_BMP_SENSOR_NAME = 'UsDev2Bmp280';

    public const REGULAR_3_BMP_SENSOR_NAME = 'UsDev3Bmp280';


    public const ADMIN_1_SOIL_SENSOR_NAME = 'AdminDev1Soil';

    public const ADMIN_2_SOIL_SENSOR_NAME = 'AdminDev2Soil';

    public const ADMIN_3_SOIL_SENSOR_NAME = 'AdminDev3Soil';

    public const REGULAR_1_SOIL_SENSOR_NAME = 'UsrDev1Soil';

    public const REGULAR_2_SOIL_SENSOR_NAME = 'UsrDev2Soil';

    public const REGULAR_3_SOIL_SENSOR_NAME = 'UsrDev3Soil';


    public const ADMIN_1_RELAY_SENSOR_NAME = 'AdDev1Relay';

    public const ADMIN_2_RELAY_SENSOR_NAME = 'AdDev2Relay';

    public const ADMIN_3_RELAY_SENSOR_NAME = 'AdDev3Relay';

    public const REGULAR_1_RELAY_SENSOR_NAME = 'UsDev1Relay';

    public const REGULAR_2_RELAY_SENSOR_NAME = 'UsDev2Relay';

    public const REGULAR_3_RELAY_SENSOR_NAME = 'UsDev3Relay';


    public const ADMIN_1_MOTION_SENSOR_NAME = 'AdDev1Motion';

    public const ADMIN_2_MOTION_SENSOR_NAME = 'AdDev2Motion';

    public const ADMIN_3_MOTION_SENSOR_NAME = 'AdDev3Motion';

    public const REGULAR_1_MOTION_SENSOR_NAME = 'UsDev1Motion';

    public const REGULAR_2_MOTION_SENSOR_NAME = 'UsDev2Motion';

    public const REGULAR_3_MOTION_SENSOR_NAME = 'UsDev3Motion';


    public const ADMIN_1_LDR_SENSOR_NAME = 'AdDev1LDR';

    public const ADMIN_2_LDR_SENSOR_NAME = 'AdDev2LDR';

    public const ADMIN_3_LDR_SENSOR_NAME = 'AdDev3LDR';

    public const REGULAR_1_LDR_SENSOR_NAME = 'UsDev1LDR';

    public const REGULAR_2_LDR_SENSOR_NAME = 'UsDev2LDR';

    public const REGULAR_3_LDR_SENSOR_NAME = 'UsDev3LDR';

    public const ADMIN_1_SHT_SENSOR_NAME = 'AdDev1SHT';

    public const ADMIN_2_SHT_SENSOR_NAME = 'AdDev2SHT';

    public const ADMIN_3_SHT_SENSOR_NAME = 'AdDev3SHT';

    public const REGULAR_1_SHT_SENSOR_NAME = 'UsDev1SHT';

    public const REGULAR_2_SHT_SENSOR_NAME = 'UsDev2SHT';

    public const REGULAR_3_SHT_SENSOR_NAME = 'UsDev3SHT';

    public const ADMIN_USER_ONE_DEVICE_ADMIN_GROUP_ONE_DHT = 'AdminUserOneDeviceAdminGroupOneDht';

    public const ADMIN_USER_ONE_DEVICE_REGULAR_GROUP_TWO_DHT = 'AdminUserOneDeviceRegularGroupTwoDht';

    public const REGULAR_USER_TWO_DEVICE_ADMIN_GROUP_ONE_RELAY = 'RegularUserTwoDeviceAdminGroupOneRelay';

    public const ADMIN_USER_TWO_DEVICE_ADMIN_GROUP_TWO_DALLAS = 'AdminUserTwoDeviceAdminGroupTwoDallas';

    public const ADMIN_USER_ONE_DEVICE_ADMIN_GROUP_ONE_DHT_BASE_READING_TYPE = 'AdminUserOneDeviceAdminGroupOneDhtBaseReadingType';

    public const ADMIN_USER_ONE_DEVICE_REGULAR_GROUP_TWO_DHT_BASE_READING_TYPE = 'AdminUserOneDeviceRegularGroupTwoDhtBaseReadingType';

    public const REGULAR_USER_TWO_DEVICE_ADMIN_GROUP_ONE_RELAY_BASE_READING_TYPE = 'RegularUserTwoDeviceAdminGroupOneRelayBaseReadingType';

    public const ADMIN_USER_TWO_DEVICE_ADMIN_GROUP_TWO_DALLAS_BASE_READING_TYPE = 'AdminUserTwoDeviceAdminGroupTwoDallasBaseReadingType';
    //AdminUserOneDeviceAdminGroupOneRelay
    public const ADMIN_USER_ONE_DEVICE_ADMIN_GROUP_ONE_RELAY = 'AdminUserOneDeviceAdminGroupOneRelay';

    public const ADMIN_USER_ONE_DEVICE_ADMIN_GROUP_ONE_RELAY_BASE_READING_TYPE = 'AdminUserOneDeviceAdminGroupOneRelayBaseReadingType';

    public const REGULAR_USER_ONE_DEVICE_REGULAR_GROUP_ONE_SOIL_BASE_READING_TYPE = 'RegularUserOneDeviceRegularGroupOneSoilBaseReadingType';

    public const REGULAR_USER_TWO_DEVICE_REGULAR_GROUP_TWO_DHT_BASE_READING_TYPE = 'RegularUserTwoDeviceRegularGroupTwoDhtBaseReadingType';

    public const ADMIN_USER_ONE_DEVICE_ADMIN_GROUP_ONE_MOTION_BASE_READING_TYPE = 'AdminUserOneDeviceAdminGroupOneMotionBaseReadingType';

    public const REGULAR_USER_TWO_DEVICE_REGULAR_GROUP_TWO_DHT = 'RegularUserTwoDeviceRegularGroupTwoDht';

    /** one for each of the permission check devices */
    public const PERMISSION_CHECK_SENSORS = [
        self::ADMIN_USER_ONE_DEVICE_ADMIN_GROUP_ONE_DHT => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::ADMIN_USER_ONE_DEVICE_ADMIN_GROUP_ONE],
            'sensorName' => self::ADMIN_1_DHT_SENSOR_NAME,
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Dht::NAME],
            'pinNumber' => 1,
            'temperatureBaseReadingTypeName' => self::ADMIN_USER_ONE_DEVICE_ADMIN_GROUP_ONE_DHT_BASE_READING_TYPE,
        ],
        self::ADMIN_USER_ONE_DEVICE_REGULAR_GROUP_TWO_DHT => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::ADMIN_USER_ONE_DEVICE_REGULAR_GROUP_TWO],
            'sensorName' => self::ADMIN_2_DHT_SENSOR_NAME,
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Dht::NAME],
            'pinNumber' => 1,
            'temperatureBaseReadingTypeName' => self::ADMIN_USER_ONE_DEVICE_REGULAR_GROUP_TWO_DHT_BASE_READING_TYPE,
        ],
        'AdminUserTwoDeviceAdminGroupTwoDht' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::ADMIN_USER_TWO_DEVICE_ADMIN_GROUP_TWO],
            'sensorName' => self::ADMIN_3_DHT_SENSOR_NAME,
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Dht::NAME],
            'pinNumber' => 1,
        ],
        'RegularUserOneDeviceRegularGroupOneDht' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::REGULAR_USER_ONE_DEVICE_REGULAR_GROUP_ONE],
            'sensorName' => self::REGULAR_1_DHT_SENSOR_NAME,
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Dht::NAME],
            'pinNumber' => 1,

        ],
        self::REGULAR_USER_TWO_DEVICE_REGULAR_GROUP_TWO_DHT => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::REGULAR_USER_TWO_DEVICE_REGULAR_GROUP_TWO],
            'sensorName' => self::REGULAR_2_DHT_SENSOR_NAME,
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Dht::NAME],
            'pinNumber' => 1,
            'humidityBaseReadingTypeName' => self::REGULAR_USER_TWO_DEVICE_REGULAR_GROUP_TWO_DHT_BASE_READING_TYPE,
        ],
        'RegularUserTwoDeviceAdminGroupOneDht' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::REGULAR_USER_TWO_DEVICE_ADMIN_GROUP_ONE],
            'sensorName' => self::REGULAR_3_DHT_SENSOR_NAME,
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Dht::NAME],
            'pinNumber' => 1,
        ],

        'AdminUserOneDeviceAdminGroupOneDallas' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::ADMIN_USER_ONE_DEVICE_ADMIN_GROUP_ONE],
            'sensorName' => self::ADMIN_1_DALLAS_SENSOR_NAME,
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Dallas::NAME],
            'pinNumber' => 2,
        ],

        'AdminUserOneDeviceRegularGroupTwoDallas' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::ADMIN_USER_ONE_DEVICE_REGULAR_GROUP_TWO],
            'sensorName' => self::ADMIN_2_DALLAS_SENSOR_NAME,
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Dallas::NAME],
            'pinNumber' => 2,
        ],

        self::ADMIN_USER_TWO_DEVICE_ADMIN_GROUP_TWO_DALLAS => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::ADMIN_USER_TWO_DEVICE_ADMIN_GROUP_TWO],
            'sensorName' => self::ADMIN_3_DALLAS_SENSOR_NAME,
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Dallas::NAME],
            'pinNumber' => 2,
            'temperatureBaseReadingTypeName' => self::ADMIN_USER_TWO_DEVICE_ADMIN_GROUP_TWO_DALLAS_BASE_READING_TYPE,
        ],

        'RegularUserOneDeviceRegularGroupOneDallas' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::REGULAR_USER_ONE_DEVICE_REGULAR_GROUP_ONE],
            'sensorName' => self::REGULAR_1_DALLAS_SENSOR_NAME,
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Dallas::NAME],
            'pinNumber' => 2,
        ],

        'RegularUserTwoDeviceRegularGroupTwoDallas' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::REGULAR_USER_TWO_DEVICE_REGULAR_GROUP_TWO],
            'sensorName' => self::REGULAR_2_DALLAS_SENSOR_NAME,
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Dallas::NAME],
            'pinNumber' => 2,
        ],

        'RegularUserTwoDeviceAdminGroupOneDallas' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::REGULAR_USER_TWO_DEVICE_ADMIN_GROUP_ONE],
            'sensorName' => self::REGULAR_3_DALLAS_SENSOR_NAME,
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Dallas::NAME],
            'pinNumber' => 2,
        ],

        'AdminUserOneDeviceAdminGroupOneSoil' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::ADMIN_USER_ONE_DEVICE_ADMIN_GROUP_ONE],
            'sensorName' => self::ADMIN_1_SOIL_SENSOR_NAME,
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Soil::NAME],
            'pinNumber' => 3,
        ],

        'AdminUserOneDeviceRegularGroupTwoSoil' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::ADMIN_USER_ONE_DEVICE_REGULAR_GROUP_TWO],
            'sensorName' => self::ADMIN_2_SOIL_SENSOR_NAME,
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Soil::NAME],
            'pinNumber' => 3,
        ],

        'AdminUserTwoDeviceAdminGroupTwoSoil' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::ADMIN_USER_TWO_DEVICE_ADMIN_GROUP_TWO],
            'sensorName' => self::ADMIN_3_SOIL_SENSOR_NAME,
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Soil::NAME],
            'pinNumber' => 3,
        ],

        'RegularUserOneDeviceRegularGroupOneSoil' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::REGULAR_USER_ONE_DEVICE_REGULAR_GROUP_ONE],
            'sensorName' => self::REGULAR_1_SOIL_SENSOR_NAME,
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Soil::NAME],
            'pinNumber' => 3,
            'analogBaseReadingTypeName' => self::REGULAR_USER_ONE_DEVICE_REGULAR_GROUP_ONE_SOIL_BASE_READING_TYPE,
        ],

        'RegularUserTwoDeviceRegularGroupTwoSoil' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::REGULAR_USER_TWO_DEVICE_REGULAR_GROUP_TWO],
            'sensorName' => self::REGULAR_2_SOIL_SENSOR_NAME,
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Soil::NAME],
            'pinNumber' => 3,
        ],

        'RegularUserTwoDeviceAdminGroupOneSoil' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::REGULAR_USER_TWO_DEVICE_ADMIN_GROUP_ONE],
            'sensorName' => self::REGULAR_3_SOIL_SENSOR_NAME,
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Soil::NAME],
            'pinNumber' => 3,
        ],

        'AdminUserOneDeviceAdminGroupOneBmp' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::ADMIN_USER_ONE_DEVICE_ADMIN_GROUP_ONE],
            'sensorName' => self::ADMIN_1_BMP_SENSOR_NAME,
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Bmp::NAME],
            'pinNumber' => 4,
        ],

        'AdminUserOneDeviceRegularGroupTwoBmp' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::ADMIN_USER_ONE_DEVICE_REGULAR_GROUP_TWO],
            'sensorName' => self::ADMIN_2_BMP_SENSOR_NAME,
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Bmp::NAME],
            'pinNumber' => 4,
        ],

        'AdminUserTwoDeviceAdminGroupTwoBmp' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::ADMIN_USER_TWO_DEVICE_ADMIN_GROUP_TWO],
            'sensorName' => self::ADMIN_3_BMP_SENSOR_NAME,
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Bmp::NAME],
            'pinNumber' => 4,
        ],

        'RegularUserOneDeviceRegularGroupOneBmp' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::REGULAR_USER_ONE_DEVICE_REGULAR_GROUP_ONE],
            'sensorName' => self::REGULAR_1_BMP_SENSOR_NAME,
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Bmp::NAME],
            'pinNumber' => 4,
        ],

        'RegularUserTwoDeviceRegularGroupTwoBmp' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::REGULAR_USER_TWO_DEVICE_REGULAR_GROUP_TWO],
            'sensorName' => self::REGULAR_2_BMP_SENSOR_NAME,
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Bmp::NAME],
            'pinNumber' => 4,
        ],

        'RegularUserTwoDeviceAdminGroupOneBmp' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::REGULAR_USER_TWO_DEVICE_ADMIN_GROUP_ONE],
            'sensorName' => self::REGULAR_3_BMP_SENSOR_NAME,
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Bmp::NAME],
            'pinNumber' => 4,
        ],

        self::ADMIN_USER_ONE_DEVICE_ADMIN_GROUP_ONE_RELAY => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::ADMIN_USER_ONE_DEVICE_ADMIN_GROUP_ONE],
            'sensorName' => self::ADMIN_1_RELAY_SENSOR_NAME,
            'sensors' => self::ALL_SENSOR_TYPE_DATA[GenericRelay::NAME],
            'pinNumber' => 5,
            'relayBaseReadingTypeName' => self::ADMIN_USER_ONE_DEVICE_ADMIN_GROUP_ONE_RELAY_BASE_READING_TYPE,
        ],

        'AdminUserOneDeviceRegularGroupTwoRelay' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::ADMIN_USER_ONE_DEVICE_REGULAR_GROUP_TWO],
            'sensorName' => self::ADMIN_2_RELAY_SENSOR_NAME,
            'sensors' => self::ALL_SENSOR_TYPE_DATA[GenericRelay::NAME],
            'pinNumber' => 5,
        ],

        'AdminUserTwoDeviceAdminGroupTwoRelay' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::ADMIN_USER_TWO_DEVICE_ADMIN_GROUP_TWO],
            'sensorName' => self::ADMIN_3_RELAY_SENSOR_NAME,
            'sensors' => self::ALL_SENSOR_TYPE_DATA[GenericRelay::NAME],
            'pinNumber' => 5,
        ],

        'RegularUserOneDeviceRegularGroupOneRelay' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::REGULAR_USER_ONE_DEVICE_REGULAR_GROUP_ONE],
            'sensorName' => self::REGULAR_1_RELAY_SENSOR_NAME,
            'sensors' => self::ALL_SENSOR_TYPE_DATA[GenericRelay::NAME],
            'pinNumber' => 5,
        ],

        'RegularUserTwoDeviceRegularGroupTwoRelay' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::REGULAR_USER_TWO_DEVICE_REGULAR_GROUP_TWO],
            'sensorName' => self::REGULAR_2_RELAY_SENSOR_NAME,
            'sensors' => self::ALL_SENSOR_TYPE_DATA[GenericRelay::NAME],
            'pinNumber' => 5,
        ],

        self::REGULAR_USER_TWO_DEVICE_ADMIN_GROUP_ONE_RELAY => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::REGULAR_USER_TWO_DEVICE_ADMIN_GROUP_ONE],
            'sensorName' => self::REGULAR_3_RELAY_SENSOR_NAME,
            'sensors' => self::ALL_SENSOR_TYPE_DATA[GenericRelay::NAME],
            'pinNumber' => 5,
            'baseReadingTypeName' => self::REGULAR_USER_TWO_DEVICE_ADMIN_GROUP_ONE_RELAY_BASE_READING_TYPE,
        ],

        'AdminUserOneDeviceAdminGroupOneMotion' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::ADMIN_USER_ONE_DEVICE_ADMIN_GROUP_ONE],
            'sensorName' => self::ADMIN_1_MOTION_SENSOR_NAME,
            'sensors' => self::ALL_SENSOR_TYPE_DATA[GenericMotion::NAME],
            'pinNumber' => 6,
            'motionBaseReadingTypeName' => self::ADMIN_USER_ONE_DEVICE_ADMIN_GROUP_ONE_MOTION_BASE_READING_TYPE,
        ],

        'AdminUserOneDeviceRegularGroupTwoMotion' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::ADMIN_USER_ONE_DEVICE_REGULAR_GROUP_TWO],
            'sensorName' => self::ADMIN_2_MOTION_SENSOR_NAME,
            'sensors' => self::ALL_SENSOR_TYPE_DATA[GenericMotion::NAME],
            'pinNumber' => 6,
        ],

        'AdminUserTwoDeviceAdminGroupTwoMotion' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::ADMIN_USER_TWO_DEVICE_ADMIN_GROUP_TWO],
            'sensorName' => self::ADMIN_3_MOTION_SENSOR_NAME,
            'sensors' => self::ALL_SENSOR_TYPE_DATA[GenericMotion::NAME],
            'pinNumber' => 6,
        ],

        'RegularUserOneDeviceRegularGroupOneMotion' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::REGULAR_USER_ONE_DEVICE_REGULAR_GROUP_ONE],
            'sensorName' => self::REGULAR_1_MOTION_SENSOR_NAME,
            'sensors' => self::ALL_SENSOR_TYPE_DATA[GenericMotion::NAME],
            'pinNumber' => 6,
        ],

        'RegularUserTwoDeviceAdminGroupOneMotion' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::REGULAR_USER_TWO_DEVICE_ADMIN_GROUP_ONE],
            'sensorName' => self::REGULAR_2_MOTION_SENSOR_NAME,
            'sensors' => self::ALL_SENSOR_TYPE_DATA[GenericMotion::NAME],
            'pinNumber' => 6,
        ],

        'RegularUserTwoDeviceRegularGroupTwoMotion' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::REGULAR_USER_TWO_DEVICE_REGULAR_GROUP_TWO],
            'sensorName' => self::REGULAR_3_MOTION_SENSOR_NAME,
            'sensors' => self::ALL_SENSOR_TYPE_DATA[GenericMotion::NAME],
            'pinNumber' => 6,
        ],

        //LDR
        'AdminUserOneDeviceAdminGroupOneLDR' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::ADMIN_USER_ONE_DEVICE_ADMIN_GROUP_ONE],
            'sensorName' => self::ADMIN_1_LDR_SENSOR_NAME,
            'sensors' => self::ALL_SENSOR_TYPE_DATA[LDR::NAME],
            'pinNumber' => 7,
        ],

        'AdminUserOneDeviceRegularGroupTwoLDR' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::ADMIN_USER_ONE_DEVICE_REGULAR_GROUP_TWO],
            'sensorName' => self::ADMIN_2_LDR_SENSOR_NAME,
            'sensors' => self::ALL_SENSOR_TYPE_DATA[LDR::NAME],
            'pinNumber' => 7,
        ],

        'AdminUserTwoDeviceAdminGroupTwoLDR' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::ADMIN_USER_TWO_DEVICE_ADMIN_GROUP_TWO],
            'sensorName' => self::ADMIN_3_LDR_SENSOR_NAME,
            'sensors' => self::ALL_SENSOR_TYPE_DATA[LDR::NAME],
            'pinNumber' => 7,
        ],

        'RegularUserOneDeviceRegularGroupOneLDR' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::REGULAR_USER_ONE_DEVICE_REGULAR_GROUP_ONE],
            'sensorName' => self::REGULAR_1_LDR_SENSOR_NAME,
            'sensors' => self::ALL_SENSOR_TYPE_DATA[LDR::NAME],
            'pinNumber' => 7,
        ],

        'RegularUserTwoDeviceRegularGroupTwoLDR' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::REGULAR_USER_TWO_DEVICE_REGULAR_GROUP_TWO],
            'sensorName' => self::REGULAR_2_LDR_SENSOR_NAME,
            'sensors' => self::ALL_SENSOR_TYPE_DATA[LDR::NAME],
            'pinNumber' => 7,
        ],

        'RegularUserTwoDeviceAdminGroupOneLDR' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::REGULAR_USER_TWO_DEVICE_ADMIN_GROUP_ONE],
            'sensorName' => self::REGULAR_3_LDR_SENSOR_NAME,
            'sensors' => self::ALL_SENSOR_TYPE_DATA[LDR::NAME],
            'pinNumber' => 7,
        ],

        //SHT
        'AdminUserOneDeviceAdminGroupOneSHT' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::ADMIN_USER_ONE_DEVICE_ADMIN_GROUP_ONE],
            'sensorName' => self::ADMIN_1_SHT_SENSOR_NAME,
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Sht::NAME],
            'pinNumber' => 8,
        ],

        'AdminUserOneDeviceRegularGroupTwoSHT' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::ADMIN_USER_ONE_DEVICE_REGULAR_GROUP_TWO],
            'sensorName' => self::ADMIN_2_SHT_SENSOR_NAME,
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Sht::NAME],
            'pinNumber' => 8,
        ],

        'AdminUserTwoDeviceAdminGroupTwoSHT' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::ADMIN_USER_TWO_DEVICE_ADMIN_GROUP_TWO],
            'sensorName' => self::ADMIN_3_SHT_SENSOR_NAME,
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Sht::NAME],
            'pinNumber' => 8,
        ],

        'RegularUserOneDeviceRegularGroupOneSHT' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::REGULAR_USER_ONE_DEVICE_REGULAR_GROUP_ONE],
            'sensorName' => self::REGULAR_1_SHT_SENSOR_NAME,
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Sht::NAME],
            'pinNumber' => 8,
        ],

        'RegularUserTwoDeviceRegularGroupTwoSHT' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::REGULAR_USER_TWO_DEVICE_REGULAR_GROUP_TWO],
            'sensorName' => self::REGULAR_2_SHT_SENSOR_NAME,
            'sensors' => self::ALL_SENSOR_TYPE_DATA[Sht::NAME],
            'pinNumber' => 8,
        ],

        'RegularUserTwoDeviceAdminGroupOneSHT' => [
            'device' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::REGULAR_USER_TWO_DEVICE_ADMIN_GROUP_ONE],
            'sensorName' => self::REGULAR_3_SHT_SENSOR_NAME,
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
        foreach (self::PERMISSION_CHECK_SENSORS as $ref => $sensorDetails) {
            $minuteInterval = random_int(0, 59);
            $createdAt = (new DateTime('now'))->add(new DateInterval('PT' . $minuteInterval . 'M'));
            $sensor = new Sensor();
            $sensor->setDevice($this->getReference($sensorDetails['device']['referenceName']));
            $sensor->setSensorName($sensorDetails['sensorName']);
            $sensor->setSensorTypeID($this->getReference($sensorDetails['sensors']['alias']));
            $sensor->setCreatedBy($this->getReference(UserDataFixtures::ADMIN_USER_EMAIL_ONE));
            $sensor->setPinNumber($sensorDetails['pinNumber']);
            $this->addReference($sensorDetails['sensorName'], $sensor);
            $manager->persist($sensor);

            $newSensorType = new $sensorDetails['sensors']['object']();

            foreach ($sensorDetails['sensors']['readingTypes'] as $key => $readingType) {
                $this->setSensorObjects(
                    $ref,
                    $readingType,
                    $sensor,
                    $newSensorType,
                    $manager,
                    $sensorDetails,
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
        string $refName,
        string $readingTypeObjects,
        Sensor $newSensor,
        SensorTypeInterface $newSensorType,
        ObjectManager $manager,
        array $sensorDetails,
    ): void {
        try {
            $newObject = new $readingTypeObjects();
            if ($newObject instanceof BaseReadingTypeInterface) {
                $baseReadingType = new BaseSensorReadingType();
                $baseReadingType->setSensor($newSensor);
                $baseReadingType->setCreatedAt(new DateTimeImmutable('now'));
                $baseReadingType->setUpdatedAt();
                $manager->persist($baseReadingType);
                $manager->flush();

                $newObject->setBaseReadingType($baseReadingType);
                if (
                    !empty($sensorDetails['temperatureBaseReadingTypeName'])
                    && $newObject instanceof Temperature
                ) {
                    $this->setReference($sensorDetails['temperatureBaseReadingTypeName'], $baseReadingType);
                }
                if (
                    !empty($sensorDetails['humidityBaseReadingTypeName'])
                    && $newObject instanceof Humidity
                ) {
                    $this->setReference($sensorDetails['humidityBaseReadingTypeName'], $baseReadingType);
                }
                if (
                    !empty($sensorDetails['analogBaseReadingTypeName'])
                    && $newObject instanceof Analog
                ) {
                    $this->setReference($sensorDetails['analogBaseReadingTypeName'], $baseReadingType);
                }
                if (
                    !empty($sensorDetails['latitudeBaseReadingTypeName'])
                    && $newObject instanceof Latitude
                ) {
                    $this->setReference($sensorDetails['latitudeBaseReadingTypeName'], $baseReadingType);
                }
                if (
                    !empty($sensorDetails['motionBaseReadingTypeName'])
                    && $newObject instanceof Motion
                ) {
                    $this->setReference($sensorDetails['motionBaseReadingTypeName'], $baseReadingType);
                }
                if (
                    !empty($sensorDetails['relayBaseReadingTypeName'])
                    && $newObject instanceof Relay
                ) {
                    $this->setReference($sensorDetails['relayBaseReadingTypeName'], $baseReadingType);
                }
            }


            if ($newObject instanceof AllSensorReadingTypeInterface) {
                $newObject->setUpdatedAt();
                $newObject->setCreatedAt(new DateTimeImmutable('now'));
            }

            if ($newObject instanceof StandardReadingSensorInterface) {
                if ($newSensorType instanceof StandardSensorTypeInterface) {
                    if ($newSensorType instanceof TemperatureReadingTypeInterface && $newObject instanceof Temperature) {
                        $newObject->setCurrentReading($newSensorType->getMinTemperature());
                        $newObject->setLowReading($newSensorType->getMinTemperature());
                        $newObject->setHighReading($newSensorType->getMaxTemperature());
                    }
                    if ($newSensorType instanceof HumidityReadingTypeInterface && $newObject instanceof Humidity) {
                        $newObject->setCurrentReading($newSensorType->getMinHumidity());
                        $newObject->setLowReading($newSensorType->getMinHumidity());
                        $newObject->setHighReading($newSensorType->getMaxHumidity());
                    }
                    if ($newSensorType instanceof LatitudeReadingTypeInterface && $newObject instanceof Latitude) {
                        $newObject->setCurrentReading($newSensorType->getMinLatitude());
                        $newObject->setLowReading($newSensorType->getMinLatitude());
                        $newObject->setHighReading($newSensorType->getMaxLatitude());
                    }
                    if ($newSensorType instanceof AnalogReadingTypeInterface && $newObject instanceof Analog) {
                        $newObject->setCurrentReading($newSensorType->getMinAnalog());
                        $newObject->setLowReading($newSensorType->getMinAnalog());
                        $newObject->setHighReading($newSensorType->getMaxAnalog());
                    }
                }
            } elseif ($newObject instanceof BoolReadingSensorInterface) {
                $newObject->setCurrentReading(true);
                $newObject->setExpectedReading(true);
                $newObject->setCurrentReading(true);
                $newObject->setRequestedReading(true);
            } else {
                throw new Exception('Sensor type not found');
            }
        } catch (Exception $e) {
            dd($e, $newObject, $newSensorType, $newSensor);
        }

        $this->setReference($refName, $baseReadingType);
        $manager->persist($newObject);
    }
}
