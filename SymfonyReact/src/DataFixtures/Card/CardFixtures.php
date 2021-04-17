<?php

namespace App\DataFixtures\Card;

use App\Entity\Card\CardColour;
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

    public const COLOURS = [
       [
          'colour' => 'danger',
          'shade' => 'red',
       ],
       [
          'colour' => 'success',
          'shade' => 'green',
       ],
       [
            'colour' => 'warning',
            'shade' => 'Yellow',
       ],
       [
            'colour' => 'primary',
            'shade' => 'blue',
       ]
    ];

    public const CARD_STATES = [
        'on' => Cardstate::ON,
        'off' => Cardstate::OFF,
        'device' => Cardstate::DEVICE_ONLY,
        'room' => Cardstate::ROOM_ONLY,
    ];


    public function getOrder()
    {
        return 4;
    }

    public function load(ObjectManager $manager)
    {
        foreach (self::CARD_STATES as $state) {
            $newCardState = new Cardstate();
            $newCardState->setState($state);

            $this->setReference($state, $newCardState);
            $manager->persist($newCardState);
        }

        //Icons
        foreach (self::ICONS as $iconDetails) {
            $newIcon = new Icons();
            $newIcon->setIconName($iconDetails['name']);
            $newIcon->setDescription($iconDetails['description']);

            $this->setReference($iconDetails['name'], $newIcon);
            $manager->persist($newIcon);
        }

        foreach (self::COLOURS as $iconDetails) {
            $cardColour = new CardColour();
            $cardColour->setColour($iconDetails['colour']);
            $cardColour->setShade($iconDetails['shade']);

            $this->setReference($iconDetails['colour'], $cardColour);
            $manager->persist($cardColour);
        }


        $manager->flush();
    }
}
