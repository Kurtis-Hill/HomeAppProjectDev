<?php

namespace App\Tests\Sensors\SensorService\TriggerChecker;

use App\ORM\DataFixtures\ESP8266\SensorFixtures;
use App\Sensors\Repository\Sensors\ORM\SensorRepository;
use App\Sensors\Repository\Sensors\SensorRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SensorReadingTriggerCheckerTest extends KernelTestCase
{
    private ?EntityManagerInterface $entityManager;

    private ContainerInterface|Container $diContainer;

    private SensorRepository $sensorRepository;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->diContainer = static::getContainer();
        $this->entityManager = $this->diContainer->get('doctrine.orm.default_entity_manager');
        $this->sensorRepository = $this->diContainer->get(SensorRepositoryInterface::class);
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
        parent::tearDown();
    }

    /**
     * @dataProvider correctTriggersDataProvider
     */
    public function test_correct_triggers_are_returned(
        string $sensor,
        mixed $sensorReading,
        int $numberOfTriggers,
        ?string $time = null,
        ?string $day = null,
    ): void {
        self::markTestSkipped();
//        $sensor = $this->sensorRepository->findOneBy(['sensorName' => $sensor]);

//        self::assertCount($numberOfTriggers, 0);
    }

    public function correctTriggersDataProvider(): Generator
    {
        yield [
            'sensor' => SensorFixtures::DHT_SENSOR_NAME,
            'sensorReading' => 20,
            'time' => "21:05",
            'day' => "monday",
            'numberOfTriggers' => 1,
        ];
    }
}
