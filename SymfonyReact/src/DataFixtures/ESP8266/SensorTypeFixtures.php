<?php

namespace App\DataFixtures\ESP8266;

use App\Entity\Sensors\SensorType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SensorTypeFixtures extends Fixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $dhtSensorType = new SensorType();

        $dhtSensorType->setSensorType('DHT');
        $dhtSensorType->setDescription('Temperature and Humidity Sensor');

        $manager->persist($dhtSensorType);

        $dallasSensorType = new SensorType();

        $dallasSensorType->setSensorType('Dallas Temperature');
        $dallasSensorType->setDescription('Water Proof Temperature Sensor');

        $manager->persist($dallasSensorType);

        $soilSensorType = new SensorType();

        $soilSensorType->setSensorType('Soil');
        $soilSensorType->setDescription('Soil Moisture Sensor');

        $manager->persist($soilSensorType);

        $bmpSensorType = new SensorType();

        $bmpSensorType->setSensorType('BMP');
        $bmpSensorType->setDescription('Weather Station Sensor');

        $manager->persist($bmpSensorType);

        $manager->flush();
    }

    public function getOrder(): int
    {
        return 3;
    }
}
