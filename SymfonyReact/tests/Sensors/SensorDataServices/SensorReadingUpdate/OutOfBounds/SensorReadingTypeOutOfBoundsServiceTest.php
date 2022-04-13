<?php

namespace Sensors\SensorDataServices\SensorReadingUpdate\OutOfBounds;

use App\AppConfig\DataFixtures\ESP8266\SensorFixtures;
use App\Sensors\Entity\OutOfRangeRecordings\OutOfRangeAnalog;
use App\Sensors\Entity\OutOfRangeRecordings\OutOfRangeHumid;
use App\Sensors\Entity\OutOfRangeRecordings\OutOfRangeLatitude;
use App\Sensors\Entity\OutOfRangeRecordings\OutOfRangeTemp;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\Soil;
use App\Sensors\SensorDataServices\OutOfBounds\SensorReadingTypeOutOfBoundsService;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SensorReadingTypeOutOfBoundsServiceTest extends KernelTestCase
{
    private SensorReadingTypeOutOfBoundsService $sut;

    private ?EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();
        $this->sut = $container->get(SensorReadingTypeOutOfBoundsService::class);
        $this->entityManager = $container->get('doctrine.orm.default_entity_manager');
    }
    protected function tearDown(): void
    {
        $this->entityManager = null;
        parent::tearDown();
    }

    /**
     * @dataProvider analogOutOfBoundsSensorDataProvider
     */
    public function test_out_of_bounds_saves_out_of_range_high_readings_analog(string $sensorName, string $sensorClass): void
    {
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => SensorFixtures::SENSORS[$sensorName]]);
        $soilSensor = $this->entityManager->getRepository($sensorClass)->findOneBy(['sensorNameID' => $sensor->getSensorNameID()]);

        $analogSensor = $soilSensor->getAnalogObject();

        $highReading = $analogSensor->getHighReading();
        $analogSensor->setCurrentReading($highReading + 5);

        $this->sut->checkAndProcessOutOfBounds($analogSensor);
        $this->entityManager->flush();

        $constRecord = $this->entityManager->getRepository(OutOfRangeAnalog::class);
        $constRecordings = $constRecord->findBy(['sensorReadingTypeID' => $analogSensor->getSensorID()]);

        $constRecordings = array_pop($constRecordings);
        self::assertNotEmpty($constRecordings);
        self::assertEquals($analogSensor->getCurrentReading(), $constRecordings->getSensorReading());
    }

    /**
     * @dataProvider analogOutOfBoundsSensorDataProvider
     */
    public function test_out_of_bounds_saves_out_of_range_low_readings_analog(string $sensorName, string $sensorClass): void
    {
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => SensorFixtures::SENSORS[$sensorName]]);
        $soilSensor = $this->entityManager->getRepository($sensorClass)->findOneBy(['sensorNameID' => $sensor->getSensorNameID()]);

        $analogSensor = $soilSensor->getAnalogObject();

        $lowReading = $analogSensor->getLowReading();
        $analogSensor->setCurrentReading($lowReading - 5);

        $this->sut->checkAndProcessOutOfBounds($analogSensor);
        $this->entityManager->flush();

        $constRecord = $this->entityManager->getRepository(OutOfRangeAnalog::class);
        $constRecordings = $constRecord->findBy(['sensorReadingTypeID' => $analogSensor->getSensorID()]);

        $constRecordings = array_pop($constRecordings);
        self::assertNotEmpty($constRecordings);
        self::assertEquals($analogSensor->getCurrentReading(), $constRecordings->getSensorReading());
    }

    public function analogOutOfBoundsSensorDataProvider(): Generator
    {
        yield [
            'sensorName' => Soil::NAME,
            'sensorClass' => Soil::class
        ];
    }

    /**
     * @dataProvider temperatureOutOfBoundsSensorDataProvider
     */
    public function test_out_of_bounds_saves_out_of_range_high_readings_temperature(string $sensorName, string $sensorClass): void
    {
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => SensorFixtures::SENSORS[$sensorName]]);
        $soilSensor = $this->entityManager->getRepository($sensorClass)->findOneBy(['sensorNameID' => $sensor->getSensorNameID()]);

        $tempObject = $soilSensor->getTempObject();

        $highReading = $tempObject->getHighReading();
        $tempObject->setCurrentReading($highReading + 5);

        $this->sut->checkAndProcessOutOfBounds($tempObject);
        $this->entityManager->flush();

        $constRecord = $this->entityManager->getRepository(OutOfRangeTemp::class);
        $constRecordings = $constRecord->findBy(['sensorReadingTypeID' => $tempObject->getSensorID()]);

        $constRecordings = array_pop($constRecordings);
        self::assertNotEmpty($constRecordings);
        self::assertEquals($tempObject->getCurrentReading(), $constRecordings->getSensorReading());
    }

    /**
     * @dataProvider temperatureOutOfBoundsSensorDataProvider
     */
    public function test_out_of_bounds_saves_out_of_range_low_readings_temperature(string $sensorName, string $sensorClass): void
    {
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => SensorFixtures::SENSORS[$sensorName]]);
        $soilSensor = $this->entityManager->getRepository($sensorClass)->findOneBy(['sensorNameID' => $sensor->getSensorNameID()]);

        $analogSensor = $soilSensor->getTempObject();

        $lowReading = $analogSensor->getLowReading();
        $analogSensor->setCurrentReading($lowReading - 5);

        $this->sut->checkAndProcessOutOfBounds($analogSensor);
        $this->entityManager->flush();

        $constRecord = $this->entityManager->getRepository(OutOfRangeTemp::class);
        $constRecordings = $constRecord->findBy(['sensorReadingTypeID' => $analogSensor->getSensorID()]);

        $constRecordings = array_pop($constRecordings);
        self::assertNotEmpty($constRecordings);
        self::assertEquals($analogSensor->getCurrentReading(), $constRecordings->getSensorReading());
    }

    public function temperatureOutOfBoundsSensorDataProvider(): Generator
    {
        yield [
            'sensorName' => Bmp::NAME,
            'sensorClass' => Bmp::class
        ];

        yield [
            'sensorName' => Dallas::NAME,
            'sensorClass' => Dallas::class
        ];

        yield [
            'sensorName' => Dht::NAME,
            'sensorClass' => Dht::class
        ];
    }

    /**
     * @dataProvider humidityOutOfBoundsSensorDataProvider
     */
    public function test_out_of_bounds_saves_out_of_range_high_readings_humidity(string $sensorName, string $sensorClass): void
    {
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => SensorFixtures::SENSORS[$sensorName]]);
        $soilSensor = $this->entityManager->getRepository($sensorClass)->findOneBy(['sensorNameID' => $sensor->getSensorNameID()]);

        $humidObject = $soilSensor->getHumidObject();

        $highReading = $humidObject->getHighReading();
        $humidObject->setCurrentReading($highReading + 5);

        $this->sut->checkAndProcessOutOfBounds($humidObject);
        $this->entityManager->flush();

        $constRecord = $this->entityManager->getRepository(OutOfRangeHumid::class);
        $constRecordings = $constRecord->findBy(['sensorReadingTypeID' => $humidObject->getSensorID()]);

        $constRecordings = array_pop($constRecordings);
        self::assertNotEmpty($constRecordings);
        self::assertEquals($humidObject->getCurrentReading(), $constRecordings->getSensorReading());
    }

    /**
     * @dataProvider humidityOutOfBoundsSensorDataProvider
     */
    public function test_out_of_bounds_saves_out_of_range_low_readings_humidity(string $sensorName, string $sensorClass): void
    {
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => SensorFixtures::SENSORS[$sensorName]]);
        $soilSensor = $this->entityManager->getRepository($sensorClass)->findOneBy(['sensorNameID' => $sensor->getSensorNameID()]);

        $humidObject = $soilSensor->getHumidObject();

        $lowReading = $humidObject->getLowReading();
        $humidObject->setCurrentReading($lowReading - 5);

        $this->sut->checkAndProcessOutOfBounds($humidObject);
        $this->entityManager->flush();

        $constRecord = $this->entityManager->getRepository(OutOfRangeHumid::class);
        $constRecordings = $constRecord->findBy(['sensorReadingTypeID' => $humidObject->getSensorID()]);

        $constRecordings = array_pop($constRecordings);
        self::assertNotEmpty($constRecordings);
        self::assertEquals($humidObject->getCurrentReading(), $constRecordings->getSensorReading());
    }

    public function humidityOutOfBoundsSensorDataProvider(): Generator
    {
        yield [
            'sensorName' => Bmp::NAME,
            'sensorClass' => Bmp::class
        ];

        yield [
            'sensorName' => Dht::NAME,
            'sensorClass' => Dht::class
        ];
    }

    /**
     * @dataProvider latitudeOutOfBoundsSensorDataProvider
     */
    public function test_out_of_bounds_saves_out_of_range_high_readings_latitude(string $sensorName, string $sensorClass): void
    {
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => SensorFixtures::SENSORS[$sensorName]]);
        $soilSensor = $this->entityManager->getRepository($sensorClass)->findOneBy(['sensorNameID' => $sensor->getSensorNameID()]);

        $latitudeObject = $soilSensor->getLatitudeObject();

        $highReading = $latitudeObject->getHighReading();
        $latitudeObject->setCurrentReading($highReading + 5);

        $this->sut->checkAndProcessOutOfBounds($latitudeObject);
        $this->entityManager->flush();

        $constRecord = $this->entityManager->getRepository(OutOfRangeLatitude::class);
        $constRecordings = $constRecord->findBy(['sensorReadingTypeID' => $latitudeObject->getSensorID()]);

        $constRecordings = array_pop($constRecordings);
        self::assertNotEmpty($constRecordings);
        self::assertEquals($latitudeObject->getCurrentReading(), $constRecordings->getSensorReading());
    }

    /**
     * @dataProvider latitudeOutOfBoundsSensorDataProvider
     */
    public function test_out_of_bounds_saves_out_of_range_low_readings_latitude(string $sensorName, string $sensorClass): void
    {
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => SensorFixtures::SENSORS[$sensorName]]);
        $soilSensor = $this->entityManager->getRepository($sensorClass)->findOneBy(['sensorNameID' => $sensor->getSensorNameID()]);

        $latitudeObject = $soilSensor->getLatitudeObject();

        $lowReading = $latitudeObject->getLowReading();
        $latitudeObject->setCurrentReading($lowReading - 5);

        $this->sut->checkAndProcessOutOfBounds($latitudeObject);
        $this->entityManager->flush();

        $constRecord = $this->entityManager->getRepository(OutOfRangeLatitude::class);
        $constRecordings = $constRecord->findBy(['sensorReadingTypeID' => $latitudeObject->getSensorID()]);

        $constRecordings = array_pop($constRecordings);
        self::assertNotEmpty($constRecordings);
        self::assertEquals($latitudeObject->getCurrentReading(), $constRecordings->getSensorReading());
    }

    public function latitudeOutOfBoundsSensorDataProvider(): Generator
    {
        yield [
            'sensorName' => Bmp::NAME,
            'sensorClass' => Bmp::class
        ];
    }
}
