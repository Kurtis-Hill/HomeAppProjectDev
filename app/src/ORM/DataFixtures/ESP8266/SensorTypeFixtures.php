<?php

namespace App\ORM\DataFixtures\ESP8266;

use App\Sensors\Entity\AbstractSensorType;
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
        $dhtSensorType = new Dht();

        $dhtSensorType->setDescription('Temperature and Humidity Sensor');

        $this->addReference(Dht::NAME, $dhtSensorType);
        $manager->persist($dhtSensorType);

        $dallasSensorType = new Dallas();

        $dallasSensorType->setDescription('Water Proof Temperature Sensor');

        $this->addReference(Dallas::NAME, $dallasSensorType);
        $manager->persist($dallasSensorType);

        $soilSensorType = new Soil();

        $soilSensorType->setDescription('Soil Moisture Sensor');

        $this->addReference(Soil::NAME, $soilSensorType);
        $manager->persist($soilSensorType);

        $bmpSensorType = new Bmp();
        $bmpSensorType->setDescription('Weather Station Sensor');

        $this->addReference(Bmp::NAME, $bmpSensorType);
        $manager->persist($bmpSensorType);

        $relaySensorType = new GenericRelay();

        $relaySensorType->setDescription('Relay Sensor');

        $this->addReference(GenericRelay::NAME, $relaySensorType);
        $manager->persist($relaySensorType);

        $motionSensorType = new GenericMotion();

        $motionSensorType->setDescription('Motion Sensor');

        $this->addReference(GenericMotion::NAME, $motionSensorType);
        $manager->persist($motionSensorType);

        $ldrSensorType = new LDR();

        $ldrSensorType->setDescription('Light resistor sensor');

        $this->addReference(LDR::NAME, $ldrSensorType);
        $manager->persist($ldrSensorType);

        $shtSensorType = new Sht();

        $shtSensorType->setDescription('High Accuracy Temperature and Humidity Sensor');

        $this->addReference(Sht::NAME, $shtSensorType);
        $manager->persist($shtSensorType);


        $manager->flush();
    }
}
