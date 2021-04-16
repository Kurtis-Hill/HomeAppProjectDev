<?php

namespace App\DataFixtures\Card;

use App\Entity\Card\Cardstate;
use App\Entity\Card\Icons;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class CardFixtures extends Fixture implements OrderedFixtureInterface
{
    public const ICONS = [
        [
            'name' => 'air-freshener',
            'description' => 'Christmas tree',
        ],
        [
            'name' => 'warehouse',
            'description' =>'warehouse'
        ],
        [
            'name' => 'archway',
            'description' =>'archway'
        ],
        [
            'name' => 'baby',
            'description' =>"baby"
        ],
        [
            'name' => 'bath',
            'description' =>'bath and shower'
        ],
        [
            'name' => 'bed',
            'description' =>'bed'
        ],
        [
            'name' => 'cannabis',
            'description' =>'cannabis leaf'
        ],


        [
            'name' => 'camera',
            'description' =>'camera'
        ],
        [
            'name' => 'carrot',
            'description' =>'carrot'
        ],
        [
            'name' => 'campground',
            'description' =>'tent'
        ],
        [
            'name' => 'chart-pie',
            'description' =>'graph'
        ],
        [
            'name' => 'crosshairs',
            'description' =>'crosshair'
        ],
        [
            'name' => 'database',
            'description' =>'symbol'
        ],
        [
            'name' => 'dog',
            'description' =>'doggie'
        ],
        [
            'name' => 'dove',
            'description' =>'bird'
        ],
        [
            'name' => 'download',
            'description' =>'download logo'
        ],
        [
            'name' => 'fish',
            'description' =>'fishys'
        ],
        [
            'name' => 'flask',
            'description' =>'science beaker'
        ],
        [
            'name' => 'fort-awesome',
            'description' =>'castle'
        ],
        [
            'name' => 'mobile-alt',
            'description' =>'mobile phone'
        ],
        [
            'name' => 'php',
            'description' =>'php logo'
        ],
        [
            'name' => 'Playstation',
            'description' =>'ps1 logo'
        ],
        [
            'name' => 'power-off',
            'description' =>'shutdown logo'
        ],
        [
            'name' => 'raspberry-pi',
            'description' =>'pi logo'
        ],
        [
            'name' => 'xbox',
            'description' =>'xbox logo'
        ],
        [
            'name' => 'skull-crossbones',
            'description' =>'skull and bones'
        ],
        [   'name' => 'smoking',
            'description' =>'smoking',
        ],
];











    public function getOrder()
    {
        return 4;
    }

    public function load(ObjectManager $manager)
    {
        $newCardStateOn = new Cardstate();
        $newCardStateOn->setState('ON');

        $newCardStateOff = new Cardstate();
        $newCardStateOff->setState('OFF');

        $newCardStateIndexOnly = new Cardstate();
        $newCardStateIndexOnly->setState('INDEX_ONLY');

        $newCardStateDeviceOnly = new Cardstate();
        $newCardStateDeviceOnly->setState('DEVICE_ONLY');

        $newCardStateRoomOnly = new Cardstate();
        $newCardStateRoomOnly->setState('ROOM_ONLY');

        $this->setReference($iconDetails['name'], $newCardStateOn);
        $this->setReference($iconDetails['name'], $newCardStateOff);
        $this->setReference($iconDetails['name'], $newCardStateDeviceOnly);
        $this->setReference($iconDetails['name'], $newCardStateRoomOnly);
        $manager->persist($newCardStateOn);
        $manager->persist($newCardStateOff);
        $manager->persist($newCardStateDeviceOnly);
        $manager->persist($newCardStateRoomOnly);



        //Icons
        foreach (self::ICONS as $iconDetails) {
            $newIcon = new Icons();
            $newIcon->setIconName($iconDetails['name']);
            $newIcon->setDescription($iconDetails['description']);

            $this->setReference($iconDetails['name'], $newIcon);
            $manager->persist($newIcon);
        }








        $manager->flush();
    }
}
