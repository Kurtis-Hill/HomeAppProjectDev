<?php

namespace App\ORM\DataFixtures\Core;

use App\Common\Entity\IPLog;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class IPLogFixtures extends Fixture
{
    public const IP_ADDRESS_LOG_1 = '192.168.1.254';

    public const IP_ADDRESS_LOG_2 = '192.168.1.122';

    public function load(ObjectManager $manager): void
    {
        $ipLog = new IPLog();
        $ipLog->setIpAddress(self::IP_ADDRESS_LOG_1);

        $manager->persist($ipLog);
        $this->addReference(self::IP_ADDRESS_LOG_1, $ipLog);

        $ipLogTwo = new IPLog();
        $ipLogTwo->setIpAddress(self::IP_ADDRESS_LOG_2);

        $manager->persist($ipLogTwo);
        $this->addReference(self::IP_ADDRESS_LOG_2, $ipLogTwo);

        $manager->flush();
    }
}
