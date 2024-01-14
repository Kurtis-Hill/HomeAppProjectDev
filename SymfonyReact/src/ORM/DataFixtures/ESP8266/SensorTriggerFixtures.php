<?php

namespace App\ORM\DataFixtures\ESP8266;

use App\Common\Services\SensorTriggerUserInputToStringConvertor;
use App\ORM\DataFixtures\Core\OperatorsFixtures;
use App\ORM\DataFixtures\Core\TriggerTypeFixtures;
use App\ORM\DataFixtures\Core\UserDataFixtures;
use App\Sensors\Entity\SensorTrigger;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SensorTriggerFixtures extends Fixture implements OrderedFixtureInterface
{
    private const FIXTURE_ORDER = 11;

    public const SENSOR_TRIGGER_1 = [
        'baseReadingTypeThatTriggers' => SensorFixtures::ADMIN_USER_ONE_DEVICE_ADMIN_GROUP_ONE_DHT_BASE_READING_TYPE,
        'baseReadingTypeToTriggerID' => SensorFixtures::ADMIN_USER_ONE_DEVICE_ADMIN_GROUP_ONE_RELAY_BASE_READING_TYPE,
        'valueThatTriggers' => 20,
        'startTime' => 2100,
        'endTime' => 2200,
        'days' => [
            'monday' => true,
            'tuesday' => true,
            'wednesday' => true,
            'thursday' => true,
            'friday' => true,
            'saturday' => true,
            'sunday' => true,
        ],
        'triggerType' => TriggerTypeFixtures::RELAY_UP,
        'operatorID' => OperatorsFixtures::GREATER_THAN_OR_EQUAL_TO,
        'createdBy' => UserDataFixtures::ADMIN_USER_EMAIL_ONE,
        'createdAt' => '2021-09-26 19:30:00',
        'override' => false,
    ];

    public const SENSOR_TRIGGER_2 = [
        'baseReadingTypeThatTriggers' => SensorFixtures::ADMIN_USER_ONE_DEVICE_REGULAR_GROUP_TWO_DHT_BASE_READING_TYPE,
        'baseReadingTypeToTriggerID' => SensorFixtures::ADMIN_USER_ONE_DEVICE_ADMIN_GROUP_ONE_RELAY_BASE_READING_TYPE,
        'triggerType' => TriggerTypeFixtures::RELAY_DOWN,
        'valueThatTriggers' => 14,
        'startTime' => 800,
        'endTime' => 2000,
        'days' => [
            'monday' => true,
            'tuesday' => true,
            'wednesday' => true,
            'thursday' => true,
            'friday' => true,
            'saturday' => true,
            'sunday' => true,
        ],
        'operatorID' => OperatorsFixtures::GREATER_THAN,
        'createdBy' => UserDataFixtures::ADMIN_USER_EMAIL_ONE,
        'createdAt' => '2021-09-26 19:30:00',
        'override' => false,
    ];

    public const SENSOR_TRIGGER_3 = [
        'baseReadingTypeThatTriggers' => SensorFixtures::REGULAR_USER_ONE_DEVICE_REGULAR_GROUP_ONE_SOIL_BASE_READING_TYPE,
        'baseReadingTypeToTriggerID' => SensorFixtures::ADMIN_USER_ONE_DEVICE_ADMIN_GROUP_ONE_RELAY_BASE_READING_TYPE,
        'valueThatTriggers' => 1010,
        'startTime' => null,
        'endTime' => null,
        'days' => [
            'monday' => true,
            'tuesday' => true,
            'wednesday' => true,
            'thursday' => true,
            'friday' => true,
            'saturday' => false,
            'sunday' => false,
        ],
        'triggerType' => TriggerTypeFixtures::RELAY_DOWN,
        'operatorID' => OperatorsFixtures::LESS_THAN,
        'createdBy' => UserDataFixtures::ADMIN_USER_EMAIL_TWO,
        'createdAt' => '2021-09-26 19:30:00',
        'override' => false,
    ];

    public const SENSOR_TRIGGER_4 = [
        'baseReadingTypeThatTriggers' => SensorFixtures::ADMIN_USER_ONE_DEVICE_ADMIN_GROUP_ONE_MOTION_BASE_READING_TYPE,
        'baseReadingTypeToTriggerID' => SensorFixtures::ADMIN_USER_ONE_DEVICE_ADMIN_GROUP_ONE_RELAY_BASE_READING_TYPE,
        'sensorToTriggerID' => SensorFixtures::ADMIN_1_RELAY_SENSOR_NAME,
        'valueThatTriggers' => false,
        'startTime' => 900,
        'endTime' => 1005,
        'days' => [
            'monday' => false,
            'tuesday' => false,
            'wednesday' => false,
            'thursday' => false,
            'friday' => false,
            'saturday' => true,
            'sunday' => true,
        ],
        'triggerType' => TriggerTypeFixtures::RELAY_UP,
        'operatorID' => OperatorsFixtures::EQUALS,
        'createdBy' => UserDataFixtures::ADMIN_USER_EMAIL_TWO,
        'createdAt' => '2021-09-26 19:30:00',
        'override' => false,
    ];

    public const SENSOR_TRIGGER_5 = [
        'baseReadingTypeThatTriggers' => SensorFixtures::REGULAR_USER_TWO_DEVICE_REGULAR_GROUP_TWO_DHT_BASE_READING_TYPE,
        'baseReadingTypeToTriggerID' => SensorFixtures::ADMIN_USER_ONE_DEVICE_ADMIN_GROUP_ONE_RELAY_BASE_READING_TYPE,
        'triggerType' => TriggerTypeFixtures::RELAY_UP,
        'valueThatTriggers' => 14,
        'startTime' => 800,
        'endTime' => 2000,
        'days' => [
            'monday' => true,
            'tuesday' => true,
            'wednesday' => true,
            'thursday' => true,
            'friday' => true,
            'saturday' => true,
            'sunday' => true,
        ],
        'operatorID' => OperatorsFixtures::LESS_THAN_OR_EQUAL_TO,
        'createdBy' => UserDataFixtures::ADMIN_USER_EMAIL_TWO,
        'createdAt' => '2021-09-26 19:30:00',
        'override' => false,
    ];

    public const SENSOR_TRIGGER_6 = [
        'triggerType' => TriggerTypeFixtures::RELAY_UP,
        'baseReadingTypeThatTriggers' => SensorFixtures::ADMIN_USER_TWO_DEVICE_ADMIN_GROUP_TWO_DALLAS_BASE_READING_TYPE,
        'baseReadingTypeToTriggerID' => SensorFixtures::ADMIN_USER_ONE_DEVICE_ADMIN_GROUP_ONE_RELAY_BASE_READING_TYPE,
        'valueThatTriggers' => 55,
        'days' => [
            'monday' => true,
            'tuesday' => true,
            'wednesday' => true,
            'thursday' => true,
            'friday' => true,
            'saturday' => true,
            'sunday' => true,
        ],
        'startTime' => null,
        'endTime' => null,
        'operatorID' => OperatorsFixtures::NOT_EQUALS,
        'createdBy' => UserDataFixtures::ADMIN_USER_EMAIL_TWO,
        'createdAt' => '2021-09-26 19:30:00',
        'override' => false,
    ];

    public const SENSOR_TRIGGER_7 = [
        'baseReadingTypeThatTriggers' => SensorFixtures::ADMIN_USER_ONE_DEVICE_ADMIN_GROUP_ONE_MOTION_BASE_READING_TYPE,
        'baseReadingTypeToTriggerID' => SensorFixtures::ADMIN_USER_ONE_DEVICE_ADMIN_GROUP_ONE_RELAY_BASE_READING_TYPE,
        'sensorToTriggerID' => SensorFixtures::ADMIN_1_RELAY_SENSOR_NAME,
        'valueThatTriggers' => false,
        'startTime' => 900,
        'endTime' => 1005,
        'days' => [
            'monday' => true,
            'tuesday' => true,
            'wednesday' => true,
            'thursday' => true,
            'friday' => true,
            'saturday' => false,
            'sunday' => false,
        ],
        'triggerType' => TriggerTypeFixtures::RELAY_UP,
        'operatorID' => OperatorsFixtures::EQUALS,
        'createdBy' => UserDataFixtures::ADMIN_USER_EMAIL_TWO,
        'createdAt' => '2021-09-26 19:30:00',
        'override' => true,
    ];

    public const SENSOR_TRIGGERS = [
        self::SENSOR_TRIGGER_1,
        self::SENSOR_TRIGGER_2,
        self::SENSOR_TRIGGER_3,
        self::SENSOR_TRIGGER_4,
        self::SENSOR_TRIGGER_5,
        self::SENSOR_TRIGGER_6,
        self::SENSOR_TRIGGER_7,
    ];

    public function getOrder(): int
    {
        return self::FIXTURE_ORDER;
    }

    public function load(ObjectManager $manager): void
    {
        foreach (self::SENSOR_TRIGGERS as $sensorTrigger) {
            $sensorTriggerEntity = new SensorTrigger();
            $sensorTriggerEntity->setBaseReadingTypeThatTriggers($this->getReference($sensorTrigger['baseReadingTypeThatTriggers']));
            $sensorTriggerEntity->setBaseReadingTypeToTriggerID($this->getReference($sensorTrigger['baseReadingTypeToTriggerID']));
            $sensorTriggerEntity->setTriggerType($this->getReference($sensorTrigger['triggerType']));
            $sensorTriggerEntity->setValueThatTriggers(SensorTriggerUserInputToStringConvertor::convertMixedToString($sensorTrigger['valueThatTriggers']));
            $sensorTriggerEntity->setOperator($this->getReference($sensorTrigger['operatorID']));
            $sensorTriggerEntity->setCreatedBy($this->getReference($sensorTrigger['createdBy']));
            $sensorTriggerEntity->setStartTime($sensorTrigger['startTime']);
            $sensorTriggerEntity->setEndTime($sensorTrigger['endTime']);
            $sensorTriggerEntity->setMonday($sensorTrigger['days']['monday']);
            $sensorTriggerEntity->setTuesday($sensorTrigger['days']['tuesday']);
            $sensorTriggerEntity->setWednesday($sensorTrigger['days']['wednesday']);
            $sensorTriggerEntity->setThursday($sensorTrigger['days']['thursday']);
            $sensorTriggerEntity->setFriday($sensorTrigger['days']['friday']);
            $sensorTriggerEntity->setSaturday($sensorTrigger['days']['saturday']);
            $sensorTriggerEntity->setSunday($sensorTrigger['days']['sunday']);
            $sensorTriggerEntity->setCreatedAt(new DateTime($sensorTrigger['createdAt']));
            $sensorTriggerEntity->setOverride($sensorTrigger['override']);
            $manager->persist($sensorTriggerEntity);
        }

        $manager->flush();
    }
}
