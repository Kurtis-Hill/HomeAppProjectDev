<?php

namespace App\ORM\DataFixtures\ESP8266;

use App\Sensors\Entity\SensorType;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\GenericMotion;
use App\Sensors\Entity\SensorTypes\GenericRelay;
use App\Sensors\Entity\SensorTypes\LDR;
use App\Sensors\Entity\SensorTypes\Sht;
use App\Sensors\Entity\SensorTypes\Soil;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class SensorTypeFixtures extends Fixture implements OrderedFixtureInterface
{
    private const FIXTURE_ORDER = 7;

    public function getOrder(): int
    {
        return self::FIXTURE_ORDER;
    }

    public function load(ObjectManager $manager): void
    {
        $dhtSensorType = new SensorType();

        $dhtSensorType->setSensorType(Dht::NAME);
        $dhtSensorType->setDescription('Temperature and Humidity Sensor');

        $this->addReference(Dht::NAME, $dhtSensorType);
        $manager->persist($dhtSensorType);

        $dallasSensorType = new SensorType();

        $dallasSensorType->setSensorType(Dallas::NAME);
        $dallasSensorType->setDescription('Water Proof Temperature Sensor');

        $this->addReference(Dallas::NAME, $dallasSensorType);
        $manager->persist($dallasSensorType);

        $soilSensorType = new SensorType();

        $soilSensorType->setSensorType(Soil::NAME);
        $soilSensorType->setDescription('Soil Moisture Sensor');

        $this->addReference(Soil::NAME, $soilSensorType);
        $manager->persist($soilSensorType);

        $bmpSensorType = new SensorType();

        $bmpSensorType->setSensorType(Bmp::NAME);
        $bmpSensorType->setDescription('Weather Station Sensor');

        $this->addReference(Bmp::NAME, $bmpSensorType);
        $manager->persist($bmpSensorType);

        $relaySensorType = new SensorType();

        $relaySensorType->setSensorType(GenericRelay::NAME);
        $relaySensorType->setDescription('Relay Sensor');

        $this->addReference(GenericRelay::NAME, $relaySensorType);
        $manager->persist($relaySensorType);

        $motionSensorType = new SensorType();

        $motionSensorType->setSensorType(GenericMotion::NAME);
        $motionSensorType->setDescription('Motion Sensor');

        $this->addReference(GenericMotion::NAME, $motionSensorType);
        $manager->persist($motionSensorType);

        $ldrSensorType = new SensorType();

        $ldrSensorType->setSensorType(LDR::NAME);
        $ldrSensorType->setDescription('Light resistor sensor');

        $this->addReference(LDR::NAME, $ldrSensorType);
        $manager->persist($ldrSensorType);

        $shtSensorType = new SensorType();

        $shtSensorType->setSensorType(Sht::NAME);
        $shtSensorType->setDescription('High Accuracy Temperature and Humidity Sensor');

        $this->addReference(Sht::NAME, $shtSensorType);
        $manager->persist($shtSensorType);


        $manager->flush();
    }
}
