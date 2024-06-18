<?php

namespace App\Tests\Services\Sensor\TriggerChecker;

use App\DataFixtures\Core\OperatorsFixtures;
use App\DataFixtures\ESP8266\SensorFixtures;
use App\DataFixtures\ESP8266\SensorTriggerFixtures;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Motion;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Analog;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Humidity;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Temperature;
use App\Repository\Sensor\ReadingType\ORM\BaseSensorReadingTypeRepository;
use App\Repository\Sensor\SensorReadingType\ORM\BoolReadingBaseSensorRepository;
use App\Repository\Sensor\Sensors\SensorRepositoryInterface;
use App\Repository\Sensor\SensorTriggerRepository;
use App\Services\Sensor\Trigger\TriggerChecker\SensorReadingTriggerChecker;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SensorReadingTriggerCheckerTest extends KernelTestCase
{
    private ?EntityManagerInterface $entityManager;

    private ContainerInterface|Container $diContainer;

    private SensorTriggerRepository $sensorTriggerRepository;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->diContainer = static::getContainer();
        $this->entityManager = $this->diContainer->get('doctrine.orm.default_entity_manager');
        $this->sensorTriggerRepository = $this->diContainer->get(SensorTriggerRepository::class);
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
        array $sensorTriggerData,
        string $sensorName,
        string $readingType,
    ): void {
        /** @var \App\Repository\Sensor\SensorReadingType\ORM\StandardReadingTypeRepository $readingTypeRepository */
        $sensor = $this->diContainer->get(SensorRepositoryInterface::class)->findOneBy(['sensorName' => $sensorName]);

        $baseReadingTypeRepository = $this->diContainer->get(BaseSensorReadingTypeRepository::class);
        $baseSensorReadingType = $baseReadingTypeRepository->findBy(['sensor' => $sensor->getSensorID()]);

        $readingTypeRepository = $this->entityManager->getRepository($readingType);
        $sensorReadingType = $readingTypeRepository->findOneBy(['baseReadingType' => $baseSensorReadingType]);

        self::assertNotNull($sensorReadingType);

        $triggerDay = false;
        foreach ($sensorTriggerData['days'] as $day => $value) {
            if ($value === true) {
                $triggerDay = $day;
            }
        }
        $time = $sensorTriggerData['startTime'] ?
            $sensorTriggerData['startTime'] + 5
            : null;

        $currentValue = match ($sensorTriggerData['operatorID']) {
            OperatorsFixtures::EQUALS, OperatorsFixtures::GREATER_THAN_OR_EQUAL_TO, OperatorsFixtures::LESS_THAN_OR_EQUAL_TO => $sensorTriggerData['valueThatTriggers'],
            OperatorsFixtures::NOT_EQUALS, OperatorsFixtures::GREATER_THAN => $sensorTriggerData['valueThatTriggers'] + 1,
            OperatorsFixtures::LESS_THAN => $sensorTriggerData['valueThatTriggers'] - 1,
            default => throw new \Exception('Operator not found'),
        };
        $sensorReadingType->setCurrentReading($currentValue);

        $sut = new \App\Services\Sensor\Trigger\TriggerChecker\SensorReadingTriggerChecker($this->sensorTriggerRepository);
        $result = $sut->checkSensorForTriggers(
            $sensorReadingType,
            $triggerDay,
            $time,
        );

        self::assertCount(1, $result);
    }

    /**
     * @dataProvider correctTriggersDataProvider
     */
    public function test_no_triggers_are_returned_when_correct_sensors_wrong_opposite_operator_value(
        array $sensorTriggerData,
        string $sensorName,
        string $readingType,
    ): void {
        /** @var \App\Repository\Sensor\SensorReadingType\ORM\StandardReadingTypeRepository $readingTypeRepository */
        $sensor = $this->diContainer->get(SensorRepositoryInterface::class)->findOneBy(['sensorName' => $sensorName]);

        $baseReadingTypeRepository = $this->diContainer->get(BaseSensorReadingTypeRepository::class);
        $baseSensorReadingType = $baseReadingTypeRepository->findBy(['sensor' => $sensor->getSensorID()]);

        $readingTypeRepository = $this->entityManager->getRepository($readingType);
        $sensorReadingType = $readingTypeRepository->findOneBy(['baseReadingType' => $baseSensorReadingType]);

        self::assertNotNull($sensorReadingType);

        $boolReadingBaseSensorRepository = $this->diContainer->get(BoolReadingBaseSensorRepository::class);
        $triggerDay = false;
        foreach ($sensorTriggerData['days'] as $day => $value) {
            if ($value === true) {
                $triggerDay = $day;
            }
        }
        $time = $sensorTriggerData['startTime'] ?
            $sensorTriggerData['startTime'] + 5
            : null;

        $currentValue = match ($sensorTriggerData['operatorID']) {
            OperatorsFixtures::EQUALS, OperatorsFixtures::GREATER_THAN_OR_EQUAL_TO, OperatorsFixtures::GREATER_THAN => $sensorTriggerData['valueThatTriggers'] - 1,
            OperatorsFixtures::LESS_THAN_OR_EQUAL_TO => $sensorTriggerData['valueThatTriggers'] + 5,
            OperatorsFixtures::NOT_EQUALS => $sensorTriggerData['valueThatTriggers'],
            OperatorsFixtures::LESS_THAN => $sensorTriggerData['valueThatTriggers'] + 1,
            default => throw new \Exception('Operator not found'),
        };
        $sensorReadingType->setCurrentReading($currentValue);
        $sut = new \App\Services\Sensor\Trigger\TriggerChecker\SensorReadingTriggerChecker($this->sensorTriggerRepository, $boolReadingBaseSensorRepository);
        $result = $sut->checkSensorForTriggers(
            $sensorReadingType,
            $triggerDay,
            $time,
        );

        self::assertCount(0, $result);
    }

    /**
     * @dataProvider correctTriggersDataProvider
     */
    public function test_no_triggers_are_returned_when_correct_sensors_correct_wrong_day(
        array $sensorTriggerData,
        string $sensorName,
        string $readingType,
    ): void {
        /** @var \App\Repository\Sensor\SensorReadingType\ORM\StandardReadingTypeRepository $readingTypeRepository */
        $sensor = $this->diContainer->get(SensorRepositoryInterface::class)->findOneBy(['sensorName' => $sensorName]);

        $baseReadingTypeRepository = $this->diContainer->get(BaseSensorReadingTypeRepository::class);
        $baseSensorReadingType = $baseReadingTypeRepository->findBy(['sensor' => $sensor->getSensorID()]);

        $readingTypeRepository = $this->entityManager->getRepository($readingType);
        $sensorReadingType = $readingTypeRepository->findOneBy(['baseReadingType' => $baseSensorReadingType]);

        self::assertNotNull($sensorReadingType);

        $boolReadingBaseSensorRepository = $this->diContainer->get(BoolReadingBaseSensorRepository::class);
        $triggerDay = true;
        foreach ($sensorTriggerData['days'] as $day => $value) {
            if ($value === false) {
                $triggerDay = $day;
            }
        }
        if ($triggerDay !== true) {
            $time = $sensorTriggerData['startTime'] ?
                $sensorTriggerData['startTime'] + 5
                : null;

            $currentValue = match ($sensorTriggerData['operatorID']) {
                OperatorsFixtures::EQUALS, OperatorsFixtures::GREATER_THAN_OR_EQUAL_TO, OperatorsFixtures::LESS_THAN_OR_EQUAL_TO => $sensorTriggerData['valueThatTriggers'],
                OperatorsFixtures::NOT_EQUALS, OperatorsFixtures::GREATER_THAN => $sensorTriggerData['valueThatTriggers'] + 1,
                OperatorsFixtures::LESS_THAN => $sensorTriggerData['valueThatTriggers'] - 1,
                default => throw new \Exception('Operator not found'),
            };
            $sensorReadingType->setCurrentReading($currentValue);
            $sut = new \App\Services\Sensor\Trigger\TriggerChecker\SensorReadingTriggerChecker($this->sensorTriggerRepository, $boolReadingBaseSensorRepository);
            $result = $sut->checkSensorForTriggers(
                $sensorReadingType,
                $triggerDay,
                $time,
            );

            self::assertCount(0, $result);
        }
    }

    /**
     * @dataProvider correctTriggersDataProvider
     */
    public function test_no_triggers_are_returned_correct_data_wrong_time(
        array $sensorTriggerData,
        string $sensorName,
        string $readingType,
    ): void {
        /** @var \App\Repository\Sensor\SensorReadingType\ORM\StandardReadingTypeRepository $readingTypeRepository */
        $sensor = $this->diContainer->get(SensorRepositoryInterface::class)->findOneBy(['sensorName' => $sensorName]);

        $baseReadingTypeRepository = $this->diContainer->get(BaseSensorReadingTypeRepository::class);
        $baseSensorReadingType = $baseReadingTypeRepository->findBy(['sensor' => $sensor->getSensorID()]);

        $readingTypeRepository = $this->entityManager->getRepository($readingType);
        $sensorReadingType = $readingTypeRepository->findOneBy(['baseReadingType' => $baseSensorReadingType]);

        self::assertNotNull($sensorReadingType);

        $boolReadingBaseSensorRepository = $this->diContainer->get(BoolReadingBaseSensorRepository::class);
        $triggerDay = false;
        foreach ($sensorTriggerData['days'] as $day => $value) {
            if ($value === true) {
                $triggerDay = $day;
            }
        }
        $time = $sensorTriggerData['startTime'] ?
            $sensorTriggerData['startTime'] - 5
            : null;

        if ($time === null) {
            $time = $sensorTriggerData['endTime'] ?
                $sensorTriggerData['endTime'] + 5
                : null;
        }
        if ($time !== null) {
            $currentValue = match ($sensorTriggerData['operatorID']) {
                OperatorsFixtures::EQUALS, OperatorsFixtures::GREATER_THAN_OR_EQUAL_TO, OperatorsFixtures::LESS_THAN_OR_EQUAL_TO => $sensorTriggerData['valueThatTriggers'],
                OperatorsFixtures::NOT_EQUALS, OperatorsFixtures::GREATER_THAN => $sensorTriggerData['valueThatTriggers'] + 1,
                OperatorsFixtures::LESS_THAN => $sensorTriggerData['valueThatTriggers'] - 1,
                default => throw new \Exception('Operator not found'),
            };
            $sensorReadingType->setCurrentReading($currentValue);
            $sut = new SensorReadingTriggerChecker($this->sensorTriggerRepository, $boolReadingBaseSensorRepository);
            $result = $sut->checkSensorForTriggers(
                $sensorReadingType,
                $triggerDay,
                $time,
            );

            self::assertCount(0, $result);
        }
    }

    /**
     * @dataProvider overrideDataProvider
     */
    public function test_override_doesnt_return_when_trigger_params_are_met(
        array $sensorTriggerData,
        string $sensorName,
        string $readingType,
    ): void {
        /** @var \App\Repository\Sensor\SensorReadingType\ORM\StandardReadingTypeRepository $readingTypeRepository */
        $sensor = $this->diContainer->get(SensorRepositoryInterface::class)->findOneBy(['sensorName' => $sensorName]);

        $baseReadingTypeRepository = $this->diContainer->get(BaseSensorReadingTypeRepository::class);
        $baseSensorReadingType = $baseReadingTypeRepository->findBy(['sensor' => $sensor->getSensorID()]);

        $readingTypeRepository = $this->entityManager->getRepository($readingType);
        $sensorReadingType = $readingTypeRepository->findOneBy(['baseReadingType' => $baseSensorReadingType]);

        self::assertNotNull($sensorReadingType);

        $triggerDay = false;
        foreach ($sensorTriggerData['days'] as $day => $value) {
            if ($value === true) {
                $triggerDay = $day;
            }
        }
        self::assertNotFalse($triggerDay);

        $time = $sensorTriggerData['startTime'] ?
            $sensorTriggerData['startTime'] + 5
            : null;

        $currentValue = match ($sensorTriggerData['operatorID']) {
            OperatorsFixtures::EQUALS, OperatorsFixtures::GREATER_THAN_OR_EQUAL_TO, OperatorsFixtures::LESS_THAN_OR_EQUAL_TO => $sensorTriggerData['valueThatTriggers'],
            OperatorsFixtures::NOT_EQUALS, OperatorsFixtures::GREATER_THAN => $sensorTriggerData['valueThatTriggers'] + 1,
            OperatorsFixtures::LESS_THAN => $sensorTriggerData['valueThatTriggers'] - 1,
            default => throw new \Exception('Operator not found'),
        };

        $sensorReadingType->setCurrentReading($currentValue);
        $sut = new SensorReadingTriggerChecker($this->sensorTriggerRepository);
        $result = $sut->checkSensorForTriggers(
            $sensorReadingType,
            $triggerDay,
            $time,
        );

        self::assertCount(0, $result);
    }

    public function correctTriggersDataProvider(): Generator
    {
        yield [
            SensorTriggerFixtures::SENSOR_TRIGGER_1,
            SensorFixtures::ADMIN_1_DHT_SENSOR_NAME,
            Temperature::class,
        ];

        yield [
            SensorTriggerFixtures::SENSOR_TRIGGER_2,
            SensorFixtures::ADMIN_2_DHT_SENSOR_NAME,
            Temperature::class,
        ];

        yield [
            SensorTriggerFixtures::SENSOR_TRIGGER_3,
            SensorFixtures::REGULAR_1_SOIL_SENSOR_NAME,
            Analog::class,
        ];

        yield [
            SensorTriggerFixtures::SENSOR_TRIGGER_4,
            SensorFixtures::ADMIN_1_MOTION_SENSOR_NAME,
            Motion::class,
        ];

        yield [
            SensorTriggerFixtures::SENSOR_TRIGGER_5,
            SensorFixtures::PERMISSION_CHECK_SENSORS[SensorFixtures::REGULAR_USER_TWO_DEVICE_REGULAR_GROUP_TWO_DHT]['sensorName'],
            Humidity::class,
        ];

        yield [
            SensorTriggerFixtures::SENSOR_TRIGGER_6,
            SensorFixtures::ADMIN_3_DALLAS_SENSOR_NAME,
            Temperature::class,
        ];
    }

    public function overrideDataProvider(): Generator
    {
        yield [
            SensorTriggerFixtures::SENSOR_TRIGGER_7,
            SensorFixtures::ADMIN_1_MOTION_SENSOR_NAME,
            Motion::class,
        ];

    }
}
