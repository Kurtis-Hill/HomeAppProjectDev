<?php

namespace App\Tests\Sensors\SensorDataServices\SensorReadingUpdate\OutOfBounds;

use App\ORM\DataFixtures\ESP8266\SensorFixtures;
use App\Sensors\Entity\OutOfRangeRecordings\OutOfBoundsEntityInterface;
use App\Sensors\Entity\OutOfRangeRecordings\OutOfRangeAnalog;
use App\Sensors\Entity\OutOfRangeRecordings\OutOfRangeHumid;
use App\Sensors\Entity\OutOfRangeRecordings\OutOfRangeLatitude;
use App\Sensors\Entity\OutOfRangeRecordings\OutOfRangeTemp;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\Sht;
use App\Sensors\Entity\SensorTypes\Soil;
use App\Sensors\SensorServices\OutOfBounds\OutOfBoundsReadingTypeFacade;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SensorReadingTypeOutOfBoundsServiceTest extends KernelTestCase
{
    private OutOfBoundsReadingTypeFacade $sut;

    private ?EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();
        $this->sut = $container->get(OutOfBoundsReadingTypeFacade::class);
        $this->entityManager = $container->get('doctrine.orm.default_entity_manager');
    }

    /**
     * @dataProvider analogOutOfBoundsSensorDataProvider
     */
    public function test_out_of_bounds_saves_out_of_range_high_readings_analog(string $sensorName, string $sensorClass): void
    {
        /** @var Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => SensorFixtures::PERMISSION_CHECK_SENSORS[$sensorName]['sensorName']]);
        /** @var \App\Sensors\Entity\SensorTypes\Interfaces\AnalogReadingTypeInterface $soilSensor */
        $soilSensor = $this->entityManager->getRepository($sensorClass)->findOneBy(['sensor' => $sensor->getSensorID()]);

        $analogSensor = $soilSensor->getAnalogObject();

        $highReading = $analogSensor->getHighReading();
        $analogSensor->setCurrentReading($highReading + 5);

        $this->sut->processOutOfBounds($analogSensor);
        $this->entityManager->flush();

        $constRecord = $this->entityManager->getRepository(OutOfRangeAnalog::class);
        /** @var OutOfRangeAnalog[] $constRecordings */
        $constRecordings = $constRecord->findBy(['sensorReadingID' => $analogSensor->getSensorID()]);

        /** @var OutOfBoundsEntityInterface[] $constRecordings */
        $constRecordings = array_pop($constRecordings);
        self::assertNotEmpty($constRecordings);
        self::assertEquals($analogSensor->getCurrentReading(), $constRecordings->getSensorReading());
    }

    /**
     * @dataProvider analogOutOfBoundsSensorDataProvider
     */
    public function test_out_of_bounds_saves_out_of_range_low_readings_analog(string $sensorName, string $sensorClass): void
    {
        /** @var Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => SensorFixtures::PERMISSION_CHECK_SENSORS[$sensorName]['sensorName']]);
        /** @var \App\Sensors\Entity\SensorTypes\Interfaces\AnalogReadingTypeInterface $soilSensor */
        $soilSensor = $this->entityManager->getRepository($sensorClass)->findOneBy(['sensor' => $sensor->getSensorID()]);

        $analogSensor = $soilSensor->getAnalogObject();

        $lowReading = $analogSensor->getLowReading();
        $analogSensor->setCurrentReading($lowReading - 5);

        $this->sut->processOutOfBounds($analogSensor);
        $this->entityManager->flush();

        $constRecord = $this->entityManager->getRepository(OutOfRangeAnalog::class);
        /** @var OutOfRangeAnalog[] $constRecordings */
        $constRecordings = $constRecord->findBy(['sensorReadingID' => $analogSensor->getSensorID()]);

        /** @var OutOfBoundsEntityInterface[] $constRecordings */
        $constRecordings = array_pop($constRecordings);
        self::assertNotEmpty($constRecordings);
        self::assertEquals($analogSensor->getCurrentReading(), $constRecordings->getSensorReading());
    }

    public function analogOutOfBoundsSensorDataProvider(): Generator
    {
        yield [
            'sensorName' => 'AdminUserOneDeviceAdminGroupOneSoil',
            'sensorClass' => Soil::class
        ];
    }

    /**
     * @dataProvider temperatureOutOfBoundsSensorDataProvider
     */
    public function test_out_of_bounds_saves_out_of_range_high_readings_temperature(string $sensorName, string $sensorClass): void
    {
        /** @var Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => SensorFixtures::PERMISSION_CHECK_SENSORS[$sensorName]['sensorName']]);
        /** @var \App\Sensors\Entity\SensorTypes\Interfaces\TemperatureReadingTypeInterface $temperatureSensorType */
        $temperatureSensorType = $this->entityManager->getRepository($sensorClass)->findOneBy(['sensor' => $sensor->getSensorID()]);

        $tempObject = $temperatureSensorType->getTemperature();

        $highReading = $tempObject->getHighReading();
        $tempObject->setCurrentReading($highReading + 5);

        $this->sut->processOutOfBounds($tempObject);
        $this->entityManager->flush();

        $constRecord = $this->entityManager->getRepository(OutOfRangeTemp::class);
        $constRecordings = $constRecord->findBy(['sensorReadingID' => $tempObject->getSensorID()]);

        /** @var OutOfBoundsEntityInterface[] $constRecordings */
        $constRecordings = array_pop($constRecordings);
        self::assertNotEmpty($constRecordings);
        self::assertEquals($tempObject->getCurrentReading(), $constRecordings->getSensorReading());
    }

    /**
     * @dataProvider temperatureOutOfBoundsSensorDataProvider
     */
    public function test_out_of_bounds_saves_out_of_range_low_readings_temperature(string $sensorName, string $sensorClass): void
    {
        /** @var Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => SensorFixtures::PERMISSION_CHECK_SENSORS[$sensorName]['sensorName']]);
        /** @var \App\Sensors\Entity\SensorTypes\Interfaces\TemperatureReadingTypeInterface $soilSensor */
        $soilSensor = $this->entityManager->getRepository($sensorClass)->findOneBy(['sensor' => $sensor->getSensorID()]);

        $analogSensor = $soilSensor->getTemperature();

        $lowReading = $analogSensor->getLowReading();
        $analogSensor->setCurrentReading($lowReading - 5);

        $this->sut->processOutOfBounds($analogSensor);
        $this->entityManager->flush();

        $constRecord = $this->entityManager->getRepository(OutOfRangeTemp::class);

        $constRecordings = $constRecord->findBy(['sensorReadingID' => $analogSensor->getSensorID()]);

        /** @var OutOfBoundsEntityInterface[] $constRecordings */
        $constRecordings = array_pop($constRecordings);
        self::assertNotEmpty($constRecordings);
        self::assertEquals($analogSensor->getCurrentReading(), $constRecordings->getSensorReading());
    }

    public function temperatureOutOfBoundsSensorDataProvider(): Generator
    {
        yield [
            'sensorName' => "AdminUserOneDeviceAdminGroupOneBmp",
            'sensorClass' => Bmp::class
        ];

        yield [
            'sensorName' => "AdminUserOneDeviceAdminGroupOneDallas",
            'sensorClass' => Dallas::class
        ];

        yield [
            'sensorName' => "AdminUserOneDeviceAdminGroupOneDht",
            'sensorClass' => Dht::class
        ];

        yield [
            'sensorName' => "AdminUserOneDeviceRegularGroupTwoSHT",
            'sensorClass' => Sht::class
        ];
    }

    /**
     * @dataProvider humidityOutOfBoundsSensorDataProvider
     */
    public function test_out_of_bounds_saves_out_of_range_high_readings_humidity(string $sensorName, string $sensorClass): void
    {
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => SensorFixtures::PERMISSION_CHECK_SENSORS[$sensorName]['sensorName']]);
        $soilSensor = $this->entityManager->getRepository($sensorClass)->findOneBy(['sensor' => $sensor->getSensorID()]);

        $humidObject = $soilSensor->getHumidObject();

        $highReading = $humidObject->getHighReading();
        $humidObject->setCurrentReading($highReading + 5);

        $this->sut->processOutOfBounds($humidObject);
        $this->entityManager->flush();

        $constRecord = $this->entityManager->getRepository(OutOfRangeHumid::class);
        $constRecordings = $constRecord->findBy(['sensorReadingID' => $humidObject->getSensorID()]);

        /** @var OutOfBoundsEntityInterface[] $constRecordings */
        $constRecordings = array_pop($constRecordings);
        self::assertNotEmpty($constRecordings);
        self::assertEquals($humidObject->getCurrentReading(), $constRecordings->getSensorReading());
    }

    /**
     * @dataProvider humidityOutOfBoundsSensorDataProvider
     */
    public function test_out_of_bounds_saves_out_of_range_low_readings_humidity(string $sensorName, string $sensorClass): void
    {
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => SensorFixtures::PERMISSION_CHECK_SENSORS[$sensorName]['sensorName']]);
        $soilSensor = $this->entityManager->getRepository($sensorClass)->findOneBy(['sensor' => $sensor->getSensorID()]);

        $humidObject = $soilSensor->getHumidObject();

        $lowReading = $humidObject->getLowReading();
        $humidObject->setCurrentReading($lowReading - 5);

        $this->sut->processOutOfBounds($humidObject);
        $this->entityManager->flush();

        $constRecord = $this->entityManager->getRepository(OutOfRangeHumid::class);
        $constRecordings = $constRecord->findBy(['sensorReadingID' => $humidObject->getSensorID()]);

        /** @var OutOfBoundsEntityInterface[] $constRecordings */
        $constRecordings = array_pop($constRecordings);
        self::assertNotEmpty($constRecordings);
        self::assertEquals($humidObject->getCurrentReading(), $constRecordings->getSensorReading());
    }

    public function humidityOutOfBoundsSensorDataProvider(): Generator
    {
        yield [
            'sensorName' => "AdminUserOneDeviceAdminGroupOneBmp",
            'sensorClass' => Bmp::class
        ];

        yield [
            'sensorName' => "AdminUserOneDeviceAdminGroupOneDht",
            'sensorClass' => Dht::class
        ];

        yield [
            'sensorName' => "AdminUserOneDeviceRegularGroupTwoSHT",
            'sensorClass' => Sht::class
        ];
    }

    /**
     * @dataProvider latitudeOutOfBoundsSensorDataProvider
     */
    public function test_out_of_bounds_saves_out_of_range_high_readings_latitude(string $sensorName, string $sensorClass): void
    {
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => SensorFixtures::PERMISSION_CHECK_SENSORS[$sensorName]['sensorName']]);
        $soilSensor = $this->entityManager->getRepository($sensorClass)->findOneBy(['sensor' => $sensor->getSensorID()]);

        $latitudeObject = $soilSensor->getLatitudeObject();

        $highReading = $latitudeObject->getHighReading();
        $latitudeObject->setCurrentReading($highReading + 5);

        $this->sut->processOutOfBounds($latitudeObject);
        $this->entityManager->flush();

        $constRecord = $this->entityManager->getRepository(OutOfRangeLatitude::class);
        $constRecordings = $constRecord->findBy(['sensorReadingID' => $latitudeObject->getSensorID()]);

        /** @var OutOfBoundsEntityInterface[] $constRecordings */
        $constRecordings = array_pop($constRecordings);
        self::assertNotEmpty($constRecordings);
        self::assertEquals($latitudeObject->getCurrentReading(), $constRecordings->getSensorReading());
    }

    /**
     * @dataProvider latitudeOutOfBoundsSensorDataProvider
     */
    public function test_out_of_bounds_saves_out_of_range_low_readings_latitude(string $sensorName, string $sensorClass): void
    {
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => SensorFixtures::PERMISSION_CHECK_SENSORS[$sensorName]['sensorName']]);
        $soilSensor = $this->entityManager->getRepository($sensorClass)->findOneBy(['sensor' => $sensor->getSensorID()]);

        $latitudeObject = $soilSensor->getLatitudeObject();

        $lowReading = $latitudeObject->getLowReading();
        $latitudeObject->setCurrentReading($lowReading - 5);

        $this->sut->processOutOfBounds($latitudeObject);
        $this->entityManager->flush();

        $constRecord = $this->entityManager->getRepository(OutOfRangeLatitude::class);
        $constRecordings = $constRecord->findBy(['sensorReadingID' => $latitudeObject->getSensorID()]);

        /** @var OutOfBoundsEntityInterface[] $constRecordings */
        $constRecordings = array_pop($constRecordings);
        self::assertNotEmpty($constRecordings);
        self::assertEquals($latitudeObject->getCurrentReading(), $constRecordings->getSensorReading());
    }

    public function latitudeOutOfBoundsSensorDataProvider(): Generator
    {
        yield [
            'sensorName' => "AdminUserOneDeviceAdminGroupOneBmp",
            'sensorClass' => Bmp::class
        ];
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
        parent::tearDown();
    }
}
