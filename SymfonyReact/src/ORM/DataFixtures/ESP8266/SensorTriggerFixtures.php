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

    private const SENSOR_TRIGGER_1 = [
        'sensor-trigger-1' => [
            'sensorID' => SensorFixtures::DHT_SENSOR_NAME,
            'sensorToTriggerID' => SensorFixtures::RELAY_SENSOR_NAME,
            'valueThatTriggers' => 20,
            'startTime' => 2100,
            'endTime' => 2200,
            'monday' => true,
            'tuesday' => true,
            'wednesday' => true,
            'thursday' => true,
            'friday' => true,
            'saturday' => true,
            'sunday' => true,
            'triggerType' => TriggerTypeFixtures::RELAY_UP,
            'operatorID' => OperatorsFixtures::EQUALS,
            'createdBy' => UserDataFixtures::ADMIN_USER_EMAIL_ONE,
            'createdAt' => '2021-09-26 19:30:00',
        ],
        'sensor-trigger-2' => [
            'sensorID' => SensorFixtures::DALLAS_SENSOR_NAME,
            'sensorToTriggerID' => SensorFixtures::RELAY_SENSOR_NAME,
            'triggerType' => TriggerTypeFixtures::RELAY_DOWN,
            'valueThatTriggers' => 14,
            'startTime' => 800,
            'endTime' => 2000,
            'monday' => true,
            'tuesday' => true,
            'wednesday' => true,
            'thursday' => true,
            'friday' => true,
            'saturday' => true,
            'sunday' => true,
            'operatorID' => OperatorsFixtures::GREATER_THAN,
            'createdBy' => UserDataFixtures::ADMIN_USER_EMAIL_ONE,
            'createdAt' => '2021-09-26 19:30:00',
        ],
        'sensor-trigger-3' => [
            'sensorID' => SensorFixtures::BMP_SENSOR_NAME,
            'sensorToTriggerID' => SensorFixtures::RELAY_SENSOR_NAME,
            'valueThatTriggers' => 25,
            'startTime' => null,
            'endTime' => null,
            'monday' => true,
            'tuesday' => true,
            'wednesday' => true,
            'thursday' => true,
            'friday' => true,
            'saturday' => false,
            'sunday' => false,
            'triggerType' => TriggerTypeFixtures::RELAY_DOWN,
            'operatorID' => OperatorsFixtures::LESS_THAN,
            'createdBy' => UserDataFixtures::ADMIN_USER_EMAIL_TWO,
            'createdAt' => '2021-09-26 19:30:00',
        ],
        'sensor-trigger-4' => [
            'sensorID' => SensorFixtures::MOTION_SENSOR_NAME,
            'sensorToTriggerID' => SensorFixtures::RELAY_SENSOR_NAME,
            'valueThatTriggers' => false,
            'startTime' => 900,
            'endTime' => 1005,
            'monday' => false,
            'tuesday' => false,
            'wednesday' => false,
            'thursday' => false,
            'friday' => false,
            'saturday' => true,
            'sunday' => true,
            'triggerType' => TriggerTypeFixtures::RELAY_UP,
            'operatorID' => OperatorsFixtures::GREATER_THAN_OR_EQUAL_TO,
            'createdBy' => UserDataFixtures::ADMIN_USER_EMAIL_TWO,
            'createdAt' => '2021-09-26 19:30:00',
        ],
        'sensor-trigger-5' => [
            'sensorID' => SensorFixtures::DALLAS_SENSOR_NAME,
            'sensorToTriggerID' => SensorFixtures::RELAY_SENSOR_NAME,
            'triggerType' => TriggerTypeFixtures::RELAY_UP,
            'valueThatTriggers' => 14,
            'startTime' => 800,
            'endTime' => 2000,
            'monday' => true,
            'tuesday' => true,
            'wednesday' => true,
            'thursday' => true,
            'friday' => true,
            'saturday' => true,
            'sunday' => true,
            'operatorID' => OperatorsFixtures::LESS_THAN_OR_EQUAL_TO,
            'createdBy' => UserDataFixtures::ADMIN_USER_EMAIL_TWO,
            'createdAt' => '2021-09-26 19:30:00',
        ],
        'sensor-trigger-6' => [
            'sensorID' => SensorFixtures::SOIL_SENSOR_NAME,
            'triggerType' => TriggerTypeFixtures::RELAY_UP,
            'sensorToTriggerID' => SensorFixtures::RELAY_SENSOR_NAME,
            'valueThatTriggers' => 2542,
            'monday' => true,
            'tuesday' => true,
            'wednesday' => true,
            'thursday' => true,
            'friday' => true,
            'saturday' => true,
            'sunday' => true,
            'startTime' => null,
            'endTime' => null,
            'operatorID' => OperatorsFixtures::NOT_EQUALS,
            'createdBy' => UserDataFixtures::ADMIN_USER_EMAIL_TWO,
            'createdAt' => '2021-09-26 19:30:00',
        ],
    ];

    public function getOrder(): int
    {
        return self::FIXTURE_ORDER;
    }

    public function load(ObjectManager $manager): void
    {
        foreach (self::SENSOR_TRIGGER_1 as $sensorTrigger) {
            $sensorTriggerEntity = new SensorTrigger();
            $sensorTriggerEntity->setSensor($this->getReference($sensorTrigger['sensorID']));
            $sensorTriggerEntity->setSensorToTrigger($this->getReference($sensorTrigger['sensorToTriggerID']));
            $sensorTriggerEntity->setTriggerType($this->getReference($sensorTrigger['triggerType']));
            $sensorTriggerEntity->setValueThatTriggers(SensorTriggerUserInputToStringConvertor::convertMixedToString($sensorTrigger['valueThatTriggers']));
            $sensorTriggerEntity->setOperator($this->getReference($sensorTrigger['operatorID']));
            $sensorTriggerEntity->setCreatedBy($this->getReference($sensorTrigger['createdBy']));
            $sensorTriggerEntity->setStartTime($sensorTrigger['startTime']);
            $sensorTriggerEntity->setEndTime($sensorTrigger['endTime']);
            $sensorTriggerEntity->setMonday($sensorTrigger['monday']);
            $sensorTriggerEntity->setTuesday($sensorTrigger['tuesday']);
            $sensorTriggerEntity->setWednesday($sensorTrigger['wednesday']);
            $sensorTriggerEntity->setThursday($sensorTrigger['thursday']);
            $sensorTriggerEntity->setFriday($sensorTrigger['friday']);
            $sensorTriggerEntity->setSaturday($sensorTrigger['saturday']);
            $sensorTriggerEntity->setSunday($sensorTrigger['sunday']);
            $sensorTriggerEntity->setCreatedAt(new DateTime($sensorTrigger['createdAt']));
            $manager->persist($sensorTriggerEntity);
        }

        $manager->flush();
    }
}