<?php

namespace App\ORM\DataFixtures\UserInterface;

use App\UserInterface\Entity\Card\CardColour;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ColourFixtures extends Fixture implements OrderedFixtureInterface
{
    private const FIXTURE_ORDER = 4;

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
       ],
        [
            'colour' => 'info',
            'shade' => 'light blue',
        ],
        [
            'colour' => 'secondary',
            'shade' => 'light grey',
        ],
        [
            'colour' => 'light',
            'shade' => 'white',
        ],
        [
            'colour' => 'dark',
            'shade' => 'dark grey',
        ],
    ];

    public function getOrder(): int
    {
        return self::FIXTURE_ORDER;
    }

    public function load(ObjectManager $manager): void
    {
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
