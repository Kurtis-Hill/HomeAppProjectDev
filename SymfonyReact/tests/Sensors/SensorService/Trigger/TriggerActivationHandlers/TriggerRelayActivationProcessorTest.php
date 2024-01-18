<?php

namespace App\Tests\Sensors\SensorService\Trigger\TriggerActivationHandlers;

use App\Common\Entity\TriggerType;
use App\Sensors\Entity\SensorTrigger;
use App\Sensors\Repository\SensorTriggerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TriggerRelayActivationProcessor extends KernelTestCase
{
    private ?EntityManagerInterface $entityManager;

    private ContainerInterface|Container $diContainer;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->diContainer = static::getContainer();
        $this->entityManager = $this->diContainer->get('doctrine.orm.default_entity_manager');
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
        parent::tearDown();
    }

    public function test_relay_isnt_triggered_when_already_in_correct_state(): void
    {
        $mockTrigger = $this->createMock(SensorTrigger::class);
        $mockTriggerType = $this->createMock(TriggerType::class);
        $mockTriggerType->method('getTriggerTypeName')->willReturn(TriggerType::RELAY_UP_TRIGGER);

        

    }

}
