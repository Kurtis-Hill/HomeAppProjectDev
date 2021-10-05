<?php

namespace App\DataFixtures\ESP8266;

use App\ESPDeviceSensor\Entity\SensorType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SensorTypeFixtures extends Fixture implements OrderedFixtureInterface
{
    public const SENSOR_TYPE_DATA_FIXTURES = [
        SensorType::DHT_SENSOR,
        SensorType::BMP_SENSOR,
        SensorType::DALLAS_TEMPERATURE,
        SensorType::SOIL_SENSOR,
    ];

    public function getOrder(): int
    {
        return 4;
    }

    public function load(ObjectManager $manager): void
    {
        $dhtSensorType = new SensorType();

        $dhtSensorType->setSensorType(SensorType::DHT_SENSOR);
        $dhtSensorType->setDescription('Temperature and Humidity Sensor');

        $this->addReference(SensorType::DHT_SENSOR, $dhtSensorType);
        $manager->persist($dhtSensorType);

        $dallasSensorType = new SensorType();

        $dallasSensorType->setSensorType(SensorType::DALLAS_TEMPERATURE);
        $dallasSensorType->setDescription('Water Proof Temperature Sensor');

        $this->addReference(SensorType::DALLAS_TEMPERATURE, $dallasSensorType);
        $manager->persist($dallasSensorType);

        $soilSensorType = new SensorType();

        $soilSensorType->setSensorType(SensorType::SOIL_SENSOR);
        $soilSensorType->setDescription('Soil Moisture Sensor');

        $this->addReference(SensorType::SOIL_SENSOR, $soilSensorType);
        $manager->persist($soilSensorType);

        $bmpSensorType = new SensorType();

        $bmpSensorType->setSensorType(SensorType::BMP_SENSOR);
        $bmpSensorType->setDescription('Weather Station Sensor');

        $this->addReference(SensorType::BMP_SENSOR, $bmpSensorType);
        $manager->persist($bmpSensorType);

        $manager->flush();
    }
}
