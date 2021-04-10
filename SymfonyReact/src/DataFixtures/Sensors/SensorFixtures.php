<?php

namespace App\DataFixtures\Sensors;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class SensorFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {


        $manager->flush();
    }
}
