<?php

namespace App\Tests\Sensors\SensorService\Trigger\SensorTriggerProcessor;

use App\Common\Entity\TriggerType;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Temperature;
use App\Sensors\Entity\SensorTrigger;
use App\Sensors\Exceptions\TriggerTypeNotRecognisedException;
use App\Sensors\Factories\TriggerFactories\TriggerTypeHandlerFactory;
use App\Sensors\SensorServices\Trigger\SensorTriggerProcessor\TriggerHandler;
use App\Sensors\SensorServices\Trigger\TriggerChecker\SensorReadingTriggerCheckerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
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
        $mockTriggerTypeHandlerFactory = $this->createMock(TriggerTypeHandlerFactory::class);
        $mockLogger = $this->createMock(LoggerInterface::class);

        $mockSensorReadingTriggerChecker->method('checkSensorForTriggers')->willReturn([]);
        $mockTriggerTypeHandlerFactory->expects($this->never())->method('getTriggerTypeHandler');
        $mockLogger->expects($this->never())->method('error');

        $sut = new TriggerHandler(
            $mockSensorReadingTriggerChecker,
            $mockTriggerTypeHandlerFactory,
            $mockLogger,
        );

        $sut->handleTrigger($this->createMock(Temperature::class));
    }

    public function test_trigger_type_not_recognised_logs_error(): void
    {
        $mockSensorReadingTriggerChecker = $this->createMock(SensorReadingTriggerCheckerInterface::class);
        $mockTriggerTypeHandlerFactory = $this->createMock(TriggerTypeHandlerFactory::class);
        $mockLogger = $this->createMock(LoggerInterface::class);

        $mockTrigger = $this->createMock(SensorTrigger::class);
        $mockTriggerType = $this->createMock(TriggerType::class);
        $mockTriggerType->method('getTriggerTypeName')->willReturn('test');

        $mockTrigger->method('getTriggerType')->willReturn($mockTriggerType);
        $mockSensorReadingTriggerChecker->method('checkSensorForTriggers')->willReturn([$mockTrigger]);
        $mockTriggerTypeHandlerFactory->expects($this->once())->method('getTriggerTypeHandler')->willThrowException(new TriggerTypeNotRecognisedException());
        $mockLogger->expects($this->once())->method('error');

        $sut = new TriggerHandler(
            $mockSensorReadingTriggerChecker,
            $mockTriggerTypeHandlerFactory,
            $mockLogger,
        );

        $sut->handleTrigger($this->createMock(Temperature::class));
    }

    public function test_process_trigger_logs_error(): void
    {
        $mockSensorReadingTriggerChecker = $this->createMock(SensorReadingTriggerCheckerInterface::class);
        $mockTriggerTypeHandlerFactory = $this->createMock(TriggerTypeHandlerFactory::class);
        $mockLogger = $this->createMock(LoggerInterface::class);

        $mockTrigger = $this->createMock(SensorTrigger::class);
        $mockTriggerType = $this->createMock(TriggerType::class);
        $mockTriggerType->method('getTriggerTypeName')->willReturn('test');

        $mockTriggerTwo = $this->createMock(SensorTrigger::class);
        $mockTriggerTypeTwo = $this->createMock(TriggerType::class);
        $mockTriggerTypeTwo->method('getTriggerTypeName')->willReturn('test2');

        $mockTrigger->method('getTriggerType')->willReturn($mockTriggerType);
        $mockSensorReadingTriggerChecker->method('checkSensorForTriggers')->willReturn([$mockTrigger, $mockTriggerTwo]);

        $mockTriggerTypeHandlerFactory->expects($this->exactly(2))->method('getTriggerTypeHandler')->willReturn(new Exception);
        $mockLogger->expects($this->once())->method('error');

        $sut = new TriggerHandler(
            $mockSensorReadingTriggerChecker,
            $mockTriggerTypeHandlerFactory,
            $mockLogger,
        );

        $sut->handleTrigger($this->createMock(Temperature::class));
    }
}
