<?php

namespace App\Tests\Sensors\SensorService\Trigger\SensorTriggerProcessor;

use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Temperature;
use App\Entity\Sensor\SensorTrigger;
use App\Entity\Sensor\TriggerType;
use App\Factories\Sensor\TriggerFactories\TriggerTypeHandlerFactory;
use App\Services\Sensor\Trigger\SensorTriggerProcessor\ReadingTriggerHandler;
use App\Services\Sensor\Trigger\TriggerChecker\SensorReadingTriggerCheckerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TriggerHandlerTest extends KernelTestCase
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

    public function test_empty_triggers_does_nothing(): void
    {
        $mockSensorReadingTriggerChecker = $this->createMock(SensorReadingTriggerCheckerInterface::class);
        $triggerTypeHandlerFactory = $this->diContainer->get(TriggerTypeHandlerFactory::class);
        $mockLogger = $this->createMock(LoggerInterface::class);

        $mockSensorReadingTriggerChecker->method('checkSensorForTriggers')->willReturn([]);
        $mockLogger->expects($this->never())->method('error');

        $sut = new ReadingTriggerHandler(
            $mockSensorReadingTriggerChecker,
            $triggerTypeHandlerFactory,
            $mockLogger,
        );

        $sut->handleTrigger($this->createMock(Temperature::class));
    }

    public function test_trigger_type_not_recognised_logs_error(): void
    {
        $mockSensorReadingTriggerChecker = $this->createMock(SensorReadingTriggerCheckerInterface::class);
        $triggerTypeHandlerFactory = $this->diContainer->get(TriggerTypeHandlerFactory::class);
        $mockLogger = $this->createMock(LoggerInterface::class);

        $mockTrigger = $this->createMock(SensorTrigger::class);
        $mockTriggerType = $this->createMock(TriggerType::class);
        $mockTriggerType->method('getTriggerTypeName')->willReturn('test');

        $mockTrigger->method('getTriggerType')->willReturn($mockTriggerType);
        $mockSensorReadingTriggerChecker->method('checkSensorForTriggers')->willReturn([$mockTrigger]);
        $mockLogger->expects($this->once())->method('error');

        $sut = new \App\Services\Sensor\Trigger\SensorTriggerProcessor\ReadingTriggerHandler(
            $mockSensorReadingTriggerChecker,
            $triggerTypeHandlerFactory,
            $mockLogger,
        );

        $sut->handleTrigger($this->createMock(Temperature::class));
    }

    // factory is read only so cannot test
//    public function test_process_trigger_logs_error(): void
//    {
//        $mockSensorReadingTriggerChecker = $this->createMock(SensorReadingTriggerCheckerInterface::class);
//        $triggerTypeHandlerFactory = $this->diContainer->get(TriggerTypeHandlerFactory::class);
//        $mockLogger = $this->createMock(LoggerInterface::class);
//
//        $mockTrigger = $this->createMock(SensorTrigger::class);
//        $mockTriggerType = $this->createMock(TriggerType::class);
//        $mockTriggerType->method('getTriggerTypeName')->willReturn(TriggerType::RELAY_UP_TRIGGER);
//
//        $mockTriggerTwo = $this->createMock(SensorTrigger::class);
//        $mockTriggerTypeTwo = $this->createMock(TriggerType::class);
//        $mockTriggerTypeTwo->method('getTriggerTypeName')->willReturn(TriggerType::RELAY_UP_TRIGGER);
//
//        $mockTrigger->method('getTriggerType')->willReturn($mockTriggerType);
//        $mockSensorReadingTriggerChecker->method('checkSensorForTriggers')->willReturn([$mockTrigger, $mockTriggerTwo]);
//
//        $mockLogger->expects($this->once())->method('error');
//
//        $sut = new TriggerHandler(
//            $mockSensorReadingTriggerChecker,
//            $triggerTypeHandlerFactory,
//            $mockLogger,
//        );
//
//        $sut->handleTrigger($this->createMock(Temperature::class));
//    }
}
