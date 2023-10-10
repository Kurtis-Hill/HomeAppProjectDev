<?php

namespace App\ORM\DataFixtures\ESP8266;

use App\ORM\DataFixtures\Core\OperatorsFixtures;
use App\ORM\DataFixtures\Core\TriggerTypeFixtures;
use App\ORM\DataFixtures\Core\UserDataFixtures;
use App\Sensors\Entity\SensorTrigger;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\GenericRelay;
use App\Sensors\SensorServices\SensorTrigger\SensorTriggerConvertor;
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
            'triggerType' => TriggerTypeFixtures::RELAY_UP,
            'operatorID' => OperatorsFixtures::EQUALS,
            'createdBy' => UserDataFixtures::ADMIN_USER_EMAIL_ONE,
        ],
        'sensor-trigger-2' => [
            'sensorID' => SensorFixtures::DALLAS_SENSOR_NAME,
            'sensorToTriggerID' => SensorFixtures::RELAY_SENSOR_NAME,
            'triggerType' => TriggerTypeFixtures::RELAY_DOWN,
            'valueThatTriggers' => 14,
            'operatorID' => OperatorsFixtures::GREATER_THAN,
            'createdBy' => UserDataFixtures::ADMIN_USER_EMAIL_ONE,
        ],
        'sensor-trigger-3' => [
            'sensorID' => SensorFixtures::BMP_SENSOR_NAME,
            'sensorToTriggerID' => SensorFixtures::RELAY_SENSOR_NAME,
            'valueThatTriggers' => 25,
            'triggerType' => TriggerTypeFixtures::RELAY_DOWN,
            'operatorID' => OperatorsFixtures::LESS_THAN,
            'createdBy' => UserDataFixtures::ADMIN_USER_EMAIL_TWO,
        ],
        'sensor-trigger-4' => [
            'sensorID' => SensorFixtures::MOTION_SENSOR_NAME,
            'sensorToTriggerID' => SensorFixtures::RELAY_SENSOR_NAME,
            'valueThatTriggers' => false,
            'triggerType' => TriggerTypeFixtures::RELAY_UP,
            'operatorID' => OperatorsFixtures::GREATER_THAN_OR_EQUAL_TO,
            'createdBy' => UserDataFixtures::ADMIN_USER_EMAIL_TWO,
        ],
        'sensor-trigger-5' => [
            'sensorID' => SensorFixtures::DALLAS_SENSOR_NAME,
            'sensorToTriggerID' => SensorFixtures::RELAY_SENSOR_NAME,
            'triggerType' => TriggerTypeFixtures::RELAY_UP,
            'valueThatTriggers' => 14,
            'operatorID' => OperatorsFixtures::LESS_THAN_OR_EQUAL_TO,
            'createdBy' => UserDataFixtures::ADMIN_USER_EMAIL_TWO,
        ],
        'sensor-trigger-6' => [
            'sensorID' => SensorFixtures::SOIL_SENSOR_NAME,
            'triggerType' => TriggerTypeFixtures::RELAY_UP,
            'sensorToTriggerID' => SensorFixtures::RELAY_SENSOR_NAME,
            'valueThatTriggers' => 2542,
            'operatorID' => OperatorsFixtures::NOT_EQUALS,
            'createdBy' => UserDataFixtures::ADMIN_USER_EMAIL_TWO,
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
            $sensorTriggerEntity->setValueThatTriggers(SensorTriggerConvertor::convertMixedToString($sensorTrigger['valueThatTriggers']));
            $sensorTriggerEntity->setOperator($this->getReference($sensorTrigger['operatorID']));
            $sensorTriggerEntity->setCreatedBy($this->getReference($sensorTrigger['createdBy']));

            $manager->persist($sensorTriggerEntity);
        }

        $manager->flush();
    }
}
