<?php

namespace App\Tests\Sensors\SensorService\SensorReadingUpdate\OutOfBounds;

use App\ORM\DataFixtures\ESP8266\SensorFixtures;
use App\Sensors\Entity\ConstantRecording\ConstantlyRecordEntityInterface;
use App\Sensors\Entity\OutOfRangeRecordings\OutOfBoundsEntityInterface;
use App\Sensors\Entity\OutOfRangeRecordings\OutOfRangeAnalog;
use App\Sensors\Entity\OutOfRangeRecordings\OutOfRangeHumid;
use App\Sensors\Entity\OutOfRangeRecordings\OutOfRangeLatitude;
use App\Sensors\Entity\OutOfRangeRecordings\OutOfRangeTemp;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Temperature;
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
        /** @var Analog $analogSensor */
        $analogSensor = $this->entityManager->getRepository(Analog::class)->findBySensorID($sensor->getSensorID())[0];

        $highReading = $analogSensor->getHighReading();
        $analogSensor->setCurrentReading($highReading + 5);

        $this->sut->processOutOfBounds($analogSensor);
        $this->entityManager->flush();

        $constRecord = $this->entityManager->getRepository(OutOfRangeAnalog::class);
        /** @var ConstantlyRecordEntityInterface[] $constRecordings */
        $constRecordings = $constRecord->findBy(['baseSensorReadingType' => $analogSensor->getBaseReadingType()]);

        /** @var OutOfBoundsEntityInterface $constRecordings */
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
        /** @var Analog $analogSensor */
        $analogSensor = $this->entityManager->getRepository(Analog::class)->findBySensorID($sensor->getSensorID())[0];

        $lowReading = $analogSensor->getLowReading();
        $analogSensor->setCurrentReading($lowReading - 5);

        $this->sut->processOutOfBounds($analogSensor);
        $this->entityManager->flush();

        $constRecord = $this->entityManager->getRepository(OutOfRangeAnalog::class);
        /** @var OutOfRangeAnalog[] $constRecordings */
        $constRecordings = $constRecord->findBy(['baseSensorReadingType' => $analogSensor->getBaseReadingType()]);

        /** @var OutOfBoundsEntityInterface $constRecordings */
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
        /** @var Temperature $tempObject */
        $tempObject = $this->entityManager->getRepository(Temperature::class)->findBySensorID($sensor->getSensorID())[0];

        $highReading = $tempObject->getHighReading();
        $tempObject->setCurrentReading($highReading + 5);

        $this->sut->processOutOfBounds($tempObject);
        $this->entityManager->flush();

        $constRecord = $this->entityManager->getRepository(OutOfRangeTemp::class);
        $constRecordings = $constRecord->findBy(['baseSensorReadingType' => $tempObject->getBaseReadingType()]);

        /** @var OutOfBoundsEntityInterface $constRecordings */
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
        /** @var Temperature $temperature */
        $temperature = $this->entityManager->getRepository(Temperature::class)->findBySensorID($sensor->getSensorID())[0];

        $lowReading = $temperature->getLowReading();
        $temperature->setCurrentReading($lowReading - 5);

        $this->sut->processOutOfBounds($temperature);
        $this->entityManager->flush();

        $constRecord = $this->entityManager->getRepository(OutOfRangeTemp::class);

        $constRecordings = $constRecord->findBy(['baseSensorReadingType' => $temperature->getBaseReadingType()]);

        /** @var OutOfBoundsEntityInterface $constRecordings */
        $constRecordings = array_pop($constRecordings);
        self::assertNotEmpty($constRecordings);
        self::assertEquals($temperature->getCurrentReading(), $constRecordings->getSensorReading());
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
        /** @var Humidity $humidObject */
        $humidObject = $this->entityManager->getRepository(Humidity::class)->findBySensorID($sensor->getSensorID())[0];

        $highReading = $humidObject->getHighReading();
        $humidObject->setCurrentReading($highReading + 5);

        $this->sut->processOutOfBounds($humidObject);
        $this->entityManager->flush();

        $constRecord = $this->entityManager->getRepository(OutOfRangeHumid::class);
        $constRecordings = $constRecord->findBy(['baseSensorReadingType' => $humidObject->getBaseReadingType()]);

        /** @var OutOfBoundsEntityInterface $constRecordings */
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
        /** @var Humidity $humidObject */
        $humidObject = $this->entityManager->getRepository(Humidity::class)->findBySensorID($sensor->getSensorID())[0];

        $lowReading = $humidObject->getLowReading();
        $humidObject->setCurrentReading($lowReading - 5);

        $this->sut->processOutOfBounds($humidObject);
        $this->entityManager->flush();

        $constRecord = $this->entityManager->getRepository(OutOfRangeHumid::class);
        $constRecordings = $constRecord->findBy(['baseSensorReadingType' => $humidObject->getBaseReadingType()]);

        /** @var OutOfBoundsEntityInterface $constRecordings */
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
        /** @var Latitude $latitudeObject */
        $latitudeObject = $this->entityManager->getRepository(Latitude::class)->findBySensorID($sensor->getSensorID())[0];

        $highReading = $latitudeObject->getHighReading();
        $latitudeObject->setCurrentReading($highReading + 5);

        $this->sut->processOutOfBounds($latitudeObject);
        $this->entityManager->flush();

        $constRecord = $this->entityManager->getRepository(OutOfRangeLatitude::class);
        $constRecordings = $constRecord->findBy(['baseSensorReadingType' => $latitudeObject->getBaseReadingType()]);

        /** @var OutOfBoundsEntityInterface $constRecordings */
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
        /** @var Latitude $latitudeObject */
        $latitudeObject = $this->entityManager->getRepository(Latitude::class)->findBySensorID($sensor->getSensorID())[0];

        $lowReading = $latitudeObject->getLowReading();
        $latitudeObject->setCurrentReading($lowReading - 5);

        $this->sut->processOutOfBounds($latitudeObject);
        $this->entityManager->flush();

        $constRecord = $this->entityManager->getRepository(OutOfRangeLatitude::class);
        $constRecordings = $constRecord->findBy(['baseSensorReadingType' => $latitudeObject->getBaseReadingType()]);

        /** @var OutOfBoundsEntityInterface $constRecordings */
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
