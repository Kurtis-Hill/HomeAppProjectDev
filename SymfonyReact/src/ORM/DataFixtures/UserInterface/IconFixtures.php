<?php

namespace App\ORM\DataFixtures\UserInterface;

use App\UserInterface\Entity\Icons;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class IconFixtures extends Fixture implements OrderedFixtureInterface
{
    private const FIXTURE_ORDER = 5;

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

    public function getOrder(): int
    {
        return self::FIXTURE_ORDER;
    }

    public function load(ObjectManager $manager): void
    {
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
