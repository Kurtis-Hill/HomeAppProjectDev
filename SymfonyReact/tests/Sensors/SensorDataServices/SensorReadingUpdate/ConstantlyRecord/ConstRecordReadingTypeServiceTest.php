<?php

namespace App\Tests\Sensors\SensorDataServices\SensorReadingUpdate\ConstantlyRecord;

use App\ORM\DataFixtures\ESP8266\SensorFixtures;
use App\Sensors\Entity\ConstantRecording\ConstAnalog;
use App\Sensors\Entity\ConstantRecording\ConstantlyRecordEntityInterface;
use App\Sensors\Entity\ConstantRecording\ConstHumid;
use App\Sensors\Entity\ConstantRecording\ConstLatitude;
use App\Sensors\Entity\ConstantRecording\ConstTemp;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\Interfaces\AnalogSensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\HumiditySensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\LatitudeSensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\TemperatureSensorTypeInterface;
use App\Sensors\Entity\SensorTypes\Soil;
use App\Sensors\SensorServices\ConstantlyRecord\ConstRecordReadingTypeFacadeHandler;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ConstRecordReadingTypeServiceTest extends KernelTestCase
{
    private ConstRecordReadingTypeFacadeHandler $sut;

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
        /** @var Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => SensorFixtures::PERMISSION_CHECK_SENSORS[$sensorName]['sensorName']]);
        /** @var AnalogSensorTypeInterface $soilSensor */
        $soilSensor = $this->entityManager->getRepository($sensorClass)->findOneBy(['sensor' => $sensor->getSensorID()]);

        $analogSensor = $soilSensor->getAnalogObject();
        $analogSensor->setConstRecord(true);

        $this->sut->processConstRecord($analogSensor);
        $this->entityManager->flush();

        $constRecord = $this->entityManager->getRepository(ConstAnalog::class);
        /** @var ConstantlyRecordEntityInterface[] $constRecordings */
        $constRecordings = $constRecord->findBy(['sensorReadingID' => $analogSensor->getSensorID()]);

        $constRecordings = array_pop($constRecordings);
        self::assertNotEmpty($constRecordings);
        self::assertEquals($analogSensor->getCurrentReading(), $constRecordings->getSensorReading());
    }

    /**
     * @dataProvider analogConstRecordSensorDataProvider
     */
    public function test_const_record_doesnt_save_in_range_readings_analog(string $sensorName, string $sensorClass): void
    {
        /** @var Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => SensorFixtures::PERMISSION_CHECK_SENSORS[$sensorName]['sensorName']]);
        /** @var AnalogSensorTypeInterface $soilSensor */
        $soilSensor = $this->entityManager->getRepository($sensorClass)->findOneBy(['sensor' => $sensor->getSensorID()]);

        $analogSensor = $soilSensor->getAnalogObject();
        $analogSensor->setConstRecord(false);

        $this->sut->processConstRecord($analogSensor);
        $this->entityManager->flush();

        $constRecord = $this->entityManager->getRepository(ConstAnalog::class);
        /** @var ConstantlyRecordEntityInterface[] $constRecordings */
        $constRecordings = $constRecord->findBy(['sensorReading' => $analogSensor->getSensorID()]);

        $constRecordings = array_pop($constRecordings);

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
        /** @var Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => SensorFixtures::PERMISSION_CHECK_SENSORS[$sensorName]['sensorName']]);

        /** @var TemperatureSensorTypeInterface $sensorReadingTypeObject */
        $sensorReadingTypeObject = $this->entityManager->getRepository($sensorClass)->findOneBy(['sensor' => $sensor->getSensorID()]);

        $tempObject = $sensorReadingTypeObject->getTemperature();
        $tempObject->setConstRecord(true);

        $this->sut->processConstRecord($tempObject);
        $this->entityManager->flush();

        $constRecord = $this->entityManager->getRepository(ConstTemp::class);
        $constRecordings = $constRecord->findBy(['sensorReadingID' => $tempObject->getSensorID()]);
        $constRecordings = array_pop($constRecordings);

        self::assertNotEmpty($constRecordings);
        self::assertEquals($tempObject->getCurrentReading(), $constRecordings->getSensorReading());
    }

    /**
     * @dataProvider tempConstRecordSensorDataProvider
     */
    public function test_const_record_doesnt_save_in_range_readings_temp(string $sensorName, string $sensorClass): void
    {
        /** @var Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => SensorFixtures::PERMISSION_CHECK_SENSORS[$sensorName]['sensorName']]);

        /** @var TemperatureSensorTypeInterface $sensorReadingTypeObject */
        $sensorReadingTypeObject = $this->entityManager->getRepository($sensorClass)->findOneBy(['sensor' => $sensor->getSensorID()]);

        $tempObject = $sensorReadingTypeObject->getTemperature();
        $tempObject->setConstRecord(false);

        $this->sut->processConstRecord($tempObject);
        $this->entityManager->flush();

        $constRecord = $this->entityManager->getRepository(ConstTemp::class);
        $constRecordings = $constRecord->findBy(['sensorReading' => $tempObject->getSensorID()]);

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
    }

    /**
     * @dataProvider humidConstRecordSensorDataProvider
     */
    public function test_const_record_saves_out_of_range_high_readings_humid(string $sensorName, string $sensorClass): void
    {
        /** @var Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => SensorFixtures::PERMISSION_CHECK_SENSORS[$sensorName]['sensorName']]);

        /** @var HumiditySensorTypeInterface $sensorReadingTypeObject */
        $sensorReadingTypeObject = $this->entityManager->getRepository($sensorClass)->findOneBy(['sensor' => $sensor->getSensorID()]);

        $humid = $sensorReadingTypeObject->getHumidObject();
        $humid->setConstRecord(true);

        $this->sut->processConstRecord($humid);
        $this->entityManager->flush();

        $constRecord = $this->entityManager->getRepository(ConstHumid::class);
        $constRecordings = $constRecord->findBy(['sensorReadingID' => $humid->getSensorID()]);
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
        /** @var HumiditySensorTypeInterface $sensorReadingTypeObject */
        $sensorReadingTypeObject = $this->entityManager->getRepository($sensorClass)->findOneBy(['sensor' => $sensor->getSensorID()]);

        $tempObject = $sensorReadingTypeObject->getHumidObject();
        $tempObject->setConstRecord(false);

        $this->sut->processConstRecord($tempObject);
        $this->entityManager->flush();

        $constRecord = $this->entityManager->getRepository(ConstHumid::class);
        $constRecordings = $constRecord->findBy(['sensorReading' => $tempObject->getSensorID()]);

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
    }

    /**
     * @dataProvider latitudeConstRecordSensorDataProvider
     */
    public function test_const_record_saves_out_of_range_high_readings_latitude(string $sensorName, string $sensorClass): void
    {
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => SensorFixtures::PERMISSION_CHECK_SENSORS[$sensorName]['sensorName']]);
        /** @var LatitudeSensorTypeInterface $sensorReadingTypeObject */
        $sensorReadingTypeObject = $this->entityManager->getRepository($sensorClass)->findOneBy(['sensor' => $sensor->getSensorID()]);

        $latitudeObject = $sensorReadingTypeObject->getLatitudeObject();
        $latitudeObject->setConstRecord(true);

        $this->sut->processConstRecord($latitudeObject);
        $this->entityManager->flush();

        $constRecord = $this->entityManager->getRepository(ConstLatitude::class);
        $constRecordings = $constRecord->findBy(['sensorReadingID' => $latitudeObject->getSensorID()]);
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
        /** @var LatitudeSensorTypeInterface $sensorReadingTypeObject */
        $sensorReadingTypeObject = $this->entityManager->getRepository($sensorClass)->findOneBy(['sensor' => $sensor->getSensorID()]);

        $latitudeObject = $sensorReadingTypeObject->getLatitudeObject();
        $latitudeObject->setConstRecord(false);

        $this->sut->processConstRecord($latitudeObject);
        $this->entityManager->flush();

        $constRecord = $this->entityManager->getRepository(ConstLatitude::class);
        $constRecordings = $constRecord->findBy(['sensorReading' => $latitudeObject->getSensorID()]);

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
