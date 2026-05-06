<?php

namespace App\Tests\Services\Sensor\Trigger\TriggerActivationHandlers;

use App\Builders\Sensor\Internal\AMPQMessages\CurrentReadingDTOBuilders\BoolCurrentReadingUpdateDTOBuilder;
use App\Builders\Sensor\Internal\AMPQMessages\CurrentReadingDTOBuilders\UpdateSensorCurrentReadingTransportDTOBuilder;
use App\Entity\Sensor\ReadingTypes\BaseSensorReadingType;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\AbstractBoolReadingBaseSensor;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Relay;
use App\Entity\Sensor\SensorTrigger;
use App\Entity\Sensor\TriggerType;
use Doctrine\ORM\EntityManagerInterface;
use OldSound\RabbitMqBundle\RabbitMq\ProducerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;

class TriggerRelayActivationProcessorTest extends KernelTestCase
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
        /** @var \App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Relay[] $allRelaySensors */
        $allRelaySensors = $this->entityManager->getRepository(Relay::class)->findAll();
        $relaySensor = $allRelaySensors[array_rand($allRelaySensors)];
        $relaySensor->setCurrentReading(true);

        $sensorTrigger = new SensorTrigger();
        $sensorTrigger->setBaseReadingTypeToTrigger($relaySensor->getBaseReadingType());
        $sensorTrigger->setTriggerType($this->entityManager->getRepository(TriggerType::class)->findOneBy(['triggerTypeName' => TriggerType::RELAY_UP_TRIGGER]));

        $mockBaseReadingTypeToTriggerID = $this->createMock(BaseSensorReadingType::class);
        $mockBaseReadingTypeToTriggerID->method('getBaseReadingTypeID')->willReturn(1);
        $mockTriggerType = $this->createMock(TriggerType::class);
        $mockTriggerType->method('getTriggerTypeName')->willReturn(TriggerType::RELAY_UP_TRIGGER);

        $mockUpdateSensorCurrentReadingDTOBuilder = $this->createMock(UpdateSensorCurrentReadingTransportDTOBuilder::class);
        $mockUpdateSensorCurrentReadingDTOBuilder->expects($this->never())->method('buildSensorSwitchRequestConsumerMessageDTO');

        $mockCurrentReadingAMQPProducer = $this->createMock(ProducerInterface::class);
        $mockCurrentReadingAMQPProducer->expects($this->never())->method('publish');

        $boolReadingBaseSensorRepository = $this->entityManager->getRepository(AbstractBoolReadingBaseSensor::class);
        $sut = new \App\Services\Sensor\Trigger\TriggerActivationHandlers\TriggerRelayActivationProcessor(
            $mockUpdateSensorCurrentReadingDTOBuilder,
            $mockCurrentReadingAMQPProducer,
            $boolReadingBaseSensorRepository,
        );
        $sut->processTrigger($sensorTrigger);
    }

    public function test_relay_is_triggered_when_not_in_correct_state(): void
    {
        /** @var Relay[] $allRelaySensors */
        $allRelaySensors = $this->entityManager->getRepository(Relay::class)->findAll();
        $relaySensor = $allRelaySensors[array_rand($allRelaySensors)];
        $relaySensor->setCurrentReading(false);

        $sensorTrigger = new SensorTrigger();
        $sensorTrigger->setBaseReadingTypeToTrigger($relaySensor->getBaseReadingType());
        $sensorTrigger->setTriggerType($this->entityManager->getRepository(TriggerType::class)->findOneBy(['triggerTypeName' => TriggerType::RELAY_UP_TRIGGER]));

        $updateSensorCurrentReadingDTOBuilder = $this->diContainer->get(UpdateSensorCurrentReadingTransportDTOBuilder::class);
        $message = $updateSensorCurrentReadingDTOBuilder->buildSensorSwitchRequestConsumerMessageDTO(
            $relaySensor->getSensor()->getSensorID(),
            BoolCurrentReadingUpdateDTOBuilder::buildCurrentReadingUpdateDTO(
                Relay::READING_TYPE,
                true
            ),
        );
        $mockCurrentReadingAMQPProducer = $this->createMock(ProducerInterface::class);
        $mockCurrentReadingAMQPProducer->expects($this->once())->method('publish')->with(serialize($message));

        $boolReadingBaseSensorRepository = $this->entityManager->getRepository(AbstractBoolReadingBaseSensor::class);
        $sut = new \App\Services\Sensor\Trigger\TriggerActivationHandlers\TriggerRelayActivationProcessor(
            $updateSensorCurrentReadingDTOBuilder,
            $mockCurrentReadingAMQPProducer,
            $boolReadingBaseSensorRepository,
        );
        $sut->processTrigger($sensorTrigger);
    }
}
