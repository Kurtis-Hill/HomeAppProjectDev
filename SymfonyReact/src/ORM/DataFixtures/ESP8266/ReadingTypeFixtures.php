<?php

namespace App\ORM\DataFixtures\ESP8266;

use App\Sensors\Entity\ReadingTypes\ReadingTypes;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ReadingTypeFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        foreach (ReadingTypes::SENSOR_READING_TYPE_DATA as $readingTypeName => $details) {
            $readingType = new ReadingTypes();
            $readingType->setReadingType($readingTypeName);
            $manager->persist($readingType);
        }
        $manager->flush();
    }
}
