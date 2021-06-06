<?php


namespace App\DataFixtures\ESP8266;


use App\Entity\Sensors\Sensors;
use App\Entity\Sensors\SensorType;
use App\HomeAppSensorCore\ESPDeviceSensor\AbstractHomeAppUserSensorServiceCore;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ReadingTypesFixtures extends Fixture implements OrderedFixtureInterface
{
    public function getOrder()
    {
        return 5;
    }

    public function load(ObjectManager $manager)
    {
        foreach (ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES as $device) {
            foreach (SensorType::SENSOR_TYPE_DATA as $sensorType => $sensorDetails) {


                //                $newSensor = new Sensors();
//
//                $newSensor->setDeviceNameID($this->getReference($device['referenceName']));
//                $newSensor->setSensorName($sensorType.$sensorCounter);
//                $newSensor->setSensorTypeID($this->getReference($sensorType));
//
//                $this->addReference($sensorType.$sensorCounter, $newSensor);
//                $manager->persist($newSensor);
            }

           //  ++$sensorCounter;
        }
    }
}
