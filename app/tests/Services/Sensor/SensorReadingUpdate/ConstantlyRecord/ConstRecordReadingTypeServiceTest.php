<?php

namespace App\Tests\Services\Sensor\SensorReadingUpdate\ConstantlyRecord;

use App\DataFixtures\ESP8266\SensorFixtures;
use App\Entity\Sensor\ConstantRecording\ConstAnalog;
use App\Entity\Sensor\ConstantRecording\ConstHumid;
use App\Entity\Sensor\ConstantRecording\ConstLatitude;
use App\Entity\Sensor\ConstantRecording\ConstTemp;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Analog;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Humidity;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Latitude;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Temperature;
use App\Entity\Sensor\Sensor;
use App\Entity\Sensor\SensorTypes\Bmp;
use App\Entity\Sensor\SensorTypes\Dallas;
use App\Entity\Sensor\SensorTypes\Dht;
use App\Entity\Sensor\SensorTypes\Sht;
use App\Entity\Sensor\SensorTypes\Soil;
use App\Services\Sensor\ConstantlyRecord\ConstRecordReadingTypeFacadeHandler;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ConstRecordReadingTypeServiceTest extends KernelTestCase
{
    private \App\Services\Sensor\ConstantlyRecord\ConstRecordReadingTypeFacadeHandler $sut;

    private ?EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();
        $this->sut = $container->get(ConstRecordReadingTypeFacadeHandler::class);
        $this->entityManager = $container->get('doctrine.orm.default_entity_manager');
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
        parent::tearDown();
    }

    /**
     * @dataProvider analogConstRecordSensorDataProvider
     */
    public function test_const_record_saves_out_of_range_high_readings_analog(string $sensorName, string $sensorClass): void
    {
        /** @var \App\Entity\Sensor\Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => SensorFixtures::PERMISSION_CHECK_SENSORS[$sensorName]['sensorName']]);
        /** @var \App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Analog $analogSensor */
        $analogSensor = $this->entityManager->getRepository(Analog::class)->findBySensorID($sensor->getSensorID())[0];
        $analogSensor->setConstRecord(true);

//dd($analogSensor);
        $this->sut->processConstRecord($analogSensor);
        $this->entityManager->flush();

        $constRecord = $this->entityManager->getRepository(ConstAnalog::class);
        /** @var \App\Entity\Sensor\ConstantRecording\ConstantlyRecordEntityInterface[] $constRecordings */
        $constRecordings = $constRecord->findBy(['baseSensorReadingType' => $analogSensor->getBaseReadingType()]);
        $constRecordings = array_pop($constRecordings);
        self::assertNotEmpty($constRecordings);
        self::assertEquals($analogSensor->getCurrentReading(), $constRecordings->getSensorReading());
    }

    /**
     * @dataProvider analogConstRecordSensorDataProvider
     */
    public function test_const_record_doesnt_save_in_range_readings_analog(string $sensorName, string $sensorClass): void
    {
        /** @var \App\Entity\Sensor\Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => SensorFixtures::PERMISSION_CHECK_SENSORS[$sensorName]['sensorName']]);

        /** @var \App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Analog $analogSensor */
        $analogSensor = $this->entityManager->getRepository(Analog::class)->findBySensorID($sensor->getSensorID())[0];
        $analogSensor->setConstRecord(false);

        $this->sut->processConstRecord($analogSensor);
        $this->entityManager->flush();

        $constRecord = $this->entityManager->getRepository(ConstAnalog::class);
        /** @var \App\Entity\Sensor\ConstantRecording\ConstantlyRecordEntityInterface[] $constRecordings */
        $constRecordings = $constRecord->findBy(['baseSensorReadingType' => $analogSensor->getBaseReadingType()]);

        $constRecordings = array_pop($constRecordings);
//        self::assertNotEmpty($constRecordings);
        self::assertNull($constRecordings);
    }

    public function analogConstRecordSensorDataProvider(): Generator
    {
        yield [
            'sensorName' => 'RegularUserOneDeviceRegularGroupOneSoil',
            'sensorClass' => Soil::class
        ];
    }

    /**
     * @dataProvider tempConstRecordSensorDataProvider
     */
    public function test_const_record_saves_out_of_range_high_readings_temp(string $sensorName, string $sensorClass): void
    {
        /** @var \App\Entity\Sensor\Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => SensorFixtures::PERMISSION_CHECK_SENSORS[$sensorName]['sensorName']]);

        /** @var Temperature $tempObject */
        $tempObject = $this->entityManager->getRepository(Temperature::class)->findBySensorID($sensor->getSensorID())[0];

//        $tempObject = $sensorReadingTypeObject->getTemperature();
        $tempObject->setConstRecord(true);

        $this->sut->processConstRecord($tempObject);
        $this->entityManager->flush();

        $constRecord = $this->entityManager->getRepository(ConstTemp::class);
        $constRecordings = $constRecord->findBy(['baseSensorReadingType' => $tempObject->getBaseReadingType()]);
//        dd($constRecordings);
        $constRecordings = array_pop($constRecordings);

        self::assertNotEmpty($constRecordings);
        self::assertEquals($tempObject->getCurrentReading(), $constRecordings->getSensorReading());
    }

    /**
     * @dataProvider tempConstRecordSensorDataProvider
     */
    public function test_const_record_doesnt_save_in_range_readings_temp(string $sensorName, string $sensorClass): void
    {
        /** @var \App\Entity\Sensor\Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => SensorFixtures::PERMISSION_CHECK_SENSORS[$sensorName]['sensorName']]);

        /** @var Temperature $tempObject */
        $tempObject = $this->entityManager->getRepository(Temperature::class)->findBySensorID($sensor->getSensorID())[0];
        $tempObject->setConstRecord(false);

        $this->sut->processConstRecord($tempObject);
        $this->entityManager->flush();

        $constRecord = $this->entityManager->getRepository(ConstTemp::class);
        $constRecordings = $constRecord->findBy(['baseSensorReadingType' => $tempObject->getBaseReadingType()]);

        $constRecordings = array_pop($constRecordings);
        self::assertNull($constRecordings);
    }

    public function tempConstRecordSensorDataProvider(): Generator
    {
        yield [
            'sensorName' => 'AdminUserOneDeviceAdminGroupOneBmp',
            'sensorClass' => Bmp::class
        ];

        yield [
            'sensorName' => 'RegularUserTwoDeviceAdminGroupOneDallas',
            'sensorClass' => Dallas::class
        ];

        yield [
            'sensorName' => 'RegularUserTwoDeviceRegularGroupTwoDht',
            'sensorClass' => Dht::class
        ];

        yield [
            'sensorName' => 'RegularUserTwoDeviceRegularGroupTwoSHT',
            'sensorClass' => Sht::class
        ];
    }

    /**
     * @dataProvider humidConstRecordSensorDataProvider
     */
    public function test_const_record_saves_out_of_range_high_readings_humid(string $sensorName, string $sensorClass): void
    {
        /** @var \App\Entity\Sensor\Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => SensorFixtures::PERMISSION_CHECK_SENSORS[$sensorName]['sensorName']]);

        /** @var \App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Humidity $humid */
        $humid = $this->entityManager->getRepository(Humidity::class)->findBySensorID($sensor->getSensorID())[0];
        $humid->setConstRecord(true);

        $this->sut->processConstRecord($humid);
        $this->entityManager->flush();

        $constRecord = $this->entityManager->getRepository(ConstHumid::class);
        $constRecordings = $constRecord->findBy(['baseSensorReadingType' => $humid->getBaseReadingType()]);
        $constRecordings = array_pop($constRecordings);

        self::assertNotEmpty($constRecordings);
        self::assertEquals($humid->getCurrentReading(), $constRecordings->getSensorReading());
    }

    /**
     * @dataProvider humidConstRecordSensorDataProvider
     */
    public function test_const_record_doesnt_save_in_range_readings_humid(string $sensorName, string $sensorClass): void
    {
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => SensorFixtures::PERMISSION_CHECK_SENSORS[$sensorName]['sensorName']]);
        /** @var \App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Humidity $humidObject */
        $humidObject = $this->entityManager->getRepository(Humidity::class)->findBySensorID($sensor->getSensorID())[0];
        $humidObject->setConstRecord(false);

        $this->sut->processConstRecord($humidObject);
        $this->entityManager->flush();

        $constRecord = $this->entityManager->getRepository(ConstHumid::class);
        $constRecordings = $constRecord->findBy(['baseSensorReadingType' => $humidObject->getBaseReadingType()]);

        $constRecordings = array_pop($constRecordings);

        self::assertNull($constRecordings);
    }

    public function humidConstRecordSensorDataProvider(): Generator
    {
        yield [
            'sensorName' => 'AdminUserOneDeviceAdminGroupOneBmp',
            'sensorClass' => Bmp::class
        ];

        yield [
            'sensorName' => 'AdminUserOneDeviceAdminGroupOneDht',
            'sensorClass' => Dht::class
        ];

        yield [
            'sensorName' => 'AdminUserOneDeviceRegularGroupTwoSHT',
            'sensorClass' => Sht::class
        ];
    }

    /**
     * @dataProvider latitudeConstRecordSensorDataProvider
     */
    public function test_const_record_saves_out_of_range_high_readings_latitude(string $sensorName, string $sensorClass): void
    {
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => SensorFixtures::PERMISSION_CHECK_SENSORS[$sensorName]['sensorName']]);
        /** @var Latitude $latitudeObject */
        $latitudeObject = $this->entityManager->getRepository(Latitude::class)->findBySensorID($sensor->getSensorID())[0];
        $latitudeObject->setConstRecord(true);

        $this->sut->processConstRecord($latitudeObject);
        $this->entityManager->flush();

        $constRecord = $this->entityManager->getRepository(ConstLatitude::class);
        $constRecordings = $constRecord->findBy(['baseSensorReadingType' => $latitudeObject->getBaseReadingType()]);
        $constRecordings = array_pop($constRecordings);

        self::assertNotEmpty($constRecordings);
        self::assertEquals($latitudeObject->getCurrentReading(), $constRecordings->getSensorReading());
    }

    /**
     * @dataProvider latitudeConstRecordSensorDataProvider
     */
    public function test_const_record_doesnt_save_in_range_readings_latitude(string $sensorName, string $sensorClass): void
    {
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => SensorFixtures::PERMISSION_CHECK_SENSORS[$sensorName]['sensorName']]);
        /** @var \App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Latitude $latitudeObject */
        $latitudeObject = $this->entityManager->getRepository(Latitude::class)->findBySensorID($sensor->getSensorID())[0];
        $latitudeObject->setConstRecord(false);

        $this->sut->processConstRecord($latitudeObject);
        $this->entityManager->flush();

        $constRecord = $this->entityManager->getRepository(ConstLatitude::class);
        $constRecordings = $constRecord->findBy(['baseSensorReadingType' => $latitudeObject->getBaseReadingType()]);

        $constRecordings = array_pop($constRecordings);

        self::assertNull($constRecordings);
    }

    public function latitudeConstRecordSensorDataProvider(): Generator
    {
        yield [
            'sensorName' => 'AdminUserOneDeviceAdminGroupOneBmp',
            'sensorClass' => Bmp::class
        ];
    }
}
