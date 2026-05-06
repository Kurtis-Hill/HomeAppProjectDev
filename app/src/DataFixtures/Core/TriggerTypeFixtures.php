<?php

namespace App\DataFixtures\Core;

use App\Entity\Sensor\TriggerType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class TriggerTypeFixtures extends Fixture implements OrderedFixtureInterface
{
    private const FIXTURE_ORDER = 10;

    public const EMAIL = 'email';

    public const RELAY_UP = 'relayUp';

    public const RELAY_DOWN = 'relayDown';


    private const TRIGGER_TYPES = [
        self::EMAIL => [
            'triggerTypeName' => 'Email',
            'triggerTypeDescription' => 'Send an email',
        ],
        self::RELAY_UP => [
            'triggerTypeName' => 'Relay Up',
            'triggerTypeDescription' => 'Turn on a relay',
        ],
        self::RELAY_DOWN => [
            'triggerTypeName' => 'Relay Down',
            'triggerTypeDescription' => 'Turn off a relay',
        ],
    ];

    public function getOrder(): int
    {
        return self::FIXTURE_ORDER;
    }

    public function load(ObjectManager $manager): void
    {
        foreach (self::TRIGGER_TYPES as $key => $triggerTypeData) {
            $triggerType = new TriggerType();
            $triggerType->setTriggerTypeName($triggerTypeData['triggerTypeName']);
            $triggerType->setTriggerTypeDescription($triggerTypeData['triggerTypeDescription']);
            $manager->persist($triggerType);
            $this->addReference($key, $triggerType);
        }

        $manager->flush();
    }
}
