<?php


namespace App\DataFixtures\ESP8266;



use App\Entity\Sensors\Sensors;
use App\HomeAppSensorCore\ESPDeviceSensor\AbstractHomeAppUserSensorServiceCore;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SensorFixtures extends Fixture implements OrderedFixtureInterface
{
    public function getOrder()
    {
        return 4;
    }

    public function load(ObjectManager $manager)
    {
        foreach (ESP8266DeviceFixtures::DEVICES as $device) {
            $newSensor = new Sensors();

            $newSensor->setDeviceNameID($this->getReference($device['referenceName']));

            foreach (AbstractHomeAppUserSensorServiceCore::SENSOR_TYPE_DATA as $sensorName => $sensorDetails) {
               // dd($sensorName, $sensorDetails);
            }
        }
    }

}
