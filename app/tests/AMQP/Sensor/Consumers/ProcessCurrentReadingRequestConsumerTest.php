<?php

namespace AMQP\Sensor\Consumers;

use App\AMQP\Sensor\Consumers\ProcessCurrentReadingRequestConsumer;
use App\DataFixtures\ESP8266\SensorFixtures;
use App\DTOs\Sensor\Internal\CurrentReadingDTO\AMQPDTOs\UpdateSensorCurrentReadingTransportMessageDTO;
use App\DTOs\Sensor\Request\CurrentReadingRequest\ReadingTypes\AnalogCurrentReadingUpdateRequestDTO;
use App\DTOs\Sensor\Request\CurrentReadingRequest\ReadingTypes\BoolCurrentReadingUpdateRequestDTO;
use App\DTOs\Sensor\Request\CurrentReadingRequest\ReadingTypes\HumidityCurrentReadingUpdateRequestDTO;
use App\DTOs\Sensor\Request\CurrentReadingRequest\ReadingTypes\LatitudeCurrentReadingUpdateRequestDTO;
use App\DTOs\Sensor\Request\CurrentReadingRequest\ReadingTypes\TemperatureCurrentReadingUpdateRequestDTO;
use App\Entity\Device\Devices;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Motion;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Relay;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Analog;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Humidity;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Latitude;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Temperature;
use App\Entity\Sensor\Sensor;
use App\Entity\Sensor\SensorTypes\Bmp;
use App\Entity\Sensor\SensorTypes\Dallas;
use App\Entity\Sensor\SensorTypes\Dht;
use App\Entity\Sensor\SensorTypes\GenericMotion;
use App\Entity\Sensor\SensorTypes\GenericRelay;
use App\Entity\Sensor\SensorTypes\LDR;
use App\Entity\Sensor\SensorTypes\Sht;
use App\Entity\Sensor\SensorTypes\Soil;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProcessCurrentReadingRequestConsumerTest extends KernelTestCase
{
    private ProcessCurrentReadingRequestConsumer $sut;

    private ?EntityManagerInterface $entityManager;

    private const SOIL_SENSOR_TO_UPDATE = SensorFixtures::PERMISSION_CHECK_SENSORS["AdminUserOneDeviceAdminGroupOneSoil"]['sensorName'];

    private const DHT_SENSOR_TO_UPDATE = SensorFixtures::PERMISSION_CHECK_SENSORS["AdminUserOneDeviceAdminGroupOneDht"]['sensorName'];

    private const BMP_SENSOR_TO_UPDATE = SensorFixtures::PERMISSION_CHECK_SENSORS["AdminUserOneDeviceAdminGroupOneBmp"]['sensorName'];

    private const DALLAS_SENSOR_TO_UPDATE = SensorFixtures::PERMISSION_CHECK_SENSORS["AdminUserOneDeviceAdminGroupOneDallas"]['sensorName'];

    private const GENERIC_MOTION_SENSOR_TO_UPDATE = SensorFixtures::PERMISSION_CHECK_SENSORS["AdminUserTwoDeviceAdminGroupTwoMotion"]['sensorName'];

    private const GENERIC_RELAY_SENSOR_TO_UPDATE = SensorFixtures::PERMISSION_CHECK_SENSORS["AdminUserTwoDeviceAdminGroupTwoRelay"]['sensorName'];

    private const LDR_SENSOR_TO_UPDATE = SensorFixtures::PERMISSION_CHECK_SENSORS["AdminUserOneDeviceAdminGroupOneLDR"]['sensorName'];

    private const SHT_SENSOR_TO_UPDATE = SensorFixtures::PERMISSION_CHECK_SENSORS["AdminUserOneDeviceAdminGroupOneSHT"]['sensorName'];

    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();
        $this->sut = $container->get(ProcessCurrentReadingRequestConsumer::class);
        $this->entityManager = $container->get('doctrine.orm.default_entity_manager');
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
        parent::tearDown();
    }

    public function test_serialization_exception_returns_true(): void
    {
        $amqpMessage = $this->createMock(AMQPMessage::class);
        $amqpMessage->method('getBody')->willThrowException(new Exception());

        $result = $this->sut->execute($amqpMessage);
        self::assertTrue($result);
    }

    public function test_unknown_device_returns_true(): void
    {
        $deviceRepository = $this->entityManager->getRepository(Devices::class);
        while (true) {
            $randomDeviceId = random_int(1, 100000);

            /** @var Devices $device */
            $device = $deviceRepository->findOneById($randomDeviceId);
            if ($device === null) {
                break;
            }
        }
        $analogCurrentReadingUpdateMessage = new AnalogCurrentReadingUpdateRequestDTO(Soil::HIGH_SOIL_READING_BOUNDARY - 50);

        $updateCurrentReadingMessageDTO = new UpdateSensorCurrentReadingTransportMessageDTO(
            Soil::NAME,
            SensorFixtures::SENSORS[Soil::NAME],
            [$analogCurrentReadingUpdateMessage],
            $randomDeviceId
        );

        $amqpMessage = $this->createMock(AMQPMessage::class);
        $amqpMessage->method('getBody')->willReturn(serialize($updateCurrentReadingMessageDTO));

        $result = $this->sut->execute($amqpMessage);
        self::assertTrue($result);
    }

    // Soil
    public function test_soil_current_reading_message_consumers_correctly(): void
    {
        /** @var \App\Entity\Sensor\Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => self::SOIL_SENSOR_TO_UPDATE]);
        $analogCurrentReadingUpdateMessage = new AnalogCurrentReadingUpdateRequestDTO(Soil::HIGH_SOIL_READING_BOUNDARY - random_int(1, 50));
        $updateCurrentReadingMessageDTO = new UpdateSensorCurrentReadingTransportMessageDTO(
            Soil::NAME,
            self::SOIL_SENSOR_TO_UPDATE,
            [$analogCurrentReadingUpdateMessage],
            $sensor->getDevice()->getDeviceID()
        );

        $amqpMessage = new AMQPMessage(serialize($updateCurrentReadingMessageDTO));
        $result = $this->sut->execute($amqpMessage);

        /** @var \App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Analog $soilSensor */
        $standardReadingTypeRepository = $this->entityManager->getRepository(Analog::class);
        $soilSensor = $standardReadingTypeRepository->findBySensorID($sensor->getSensorID())[0];
//        dd($soilSensor);
        self::assertTrue($result);
        self::assertEquals(
            $analogCurrentReadingUpdateMessage->getCurrentReading(),
            $soilSensor->getCurrentReading(),
        );
    }

    public function test_soil_current_reading_message_consumers_wrong_current_reading_request_dto(): void
    {
        /** @var \App\Entity\Sensor\Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => self::SOIL_SENSOR_TO_UPDATE]);

        $humidCurrentReadingUpdateMessage = new HumidityCurrentReadingUpdateRequestDTO(Soil::HIGH_SOIL_READING_BOUNDARY - 50);
        $updateCurrentReadingMessageDTO = new UpdateSensorCurrentReadingTransportMessageDTO(
            Soil::NAME,
            self::SOIL_SENSOR_TO_UPDATE,
            [$humidCurrentReadingUpdateMessage],
            $sensor->getDevice()->getDeviceID()
        );

        $amqpMessage = new AMQPMessage(serialize($updateCurrentReadingMessageDTO));
        $result = $this->sut->execute($amqpMessage);

        /** @var \App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Analog $soilSensor */
        $standardReadingTypeRepository = $this->entityManager->getRepository(Analog::class);
        $soilSensor = $standardReadingTypeRepository->findBySensorID($sensor->getSensorID())[0];

        self::assertTrue($result);
        self::assertNotEquals(
            $soilSensor->getCurrentReading(),
            $humidCurrentReadingUpdateMessage->getCurrentReading()
        );
    }

    //LDR
    public function test_ldr_current_reading_message_consumers_correctly(): void
    {
        /** @var \App\Entity\Sensor\Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => self::LDR_SENSOR_TO_UPDATE]);
        $analogCurrentReadingUpdateMessage = new AnalogCurrentReadingUpdateRequestDTO(LDR::HIGH_READING - random_int(1, 50));
        $updateCurrentReadingMessageDTO = new UpdateSensorCurrentReadingTransportMessageDTO(
            LDR::NAME,
            self::LDR_SENSOR_TO_UPDATE,
            [$analogCurrentReadingUpdateMessage],
            $sensor->getDevice()->getDeviceID()
        );

        $amqpMessage = new AMQPMessage(serialize($updateCurrentReadingMessageDTO));
        $result = $this->sut->execute($amqpMessage);

        /** @var \App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Analog $soilSensor */
        $standardReadingTypeRepository = $this->entityManager->getRepository(Analog::class);
        $ldrSensor = $standardReadingTypeRepository->findBySensorID($sensor->getSensorID())[0];

        self::assertTrue($result);
        self::assertEquals(
            $analogCurrentReadingUpdateMessage->getCurrentReading(),
            $ldrSensor->getCurrentReading(),
        );
    }

    public function test_ldr_current_reading_message_consumers_wrong_current_reading_request_dto(): void
    {
        /** @var \App\Entity\Sensor\Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => self::LDR_SENSOR_TO_UPDATE]);

        $humidCurrentReadingUpdateMessage = new HumidityCurrentReadingUpdateRequestDTO(Soil::HIGH_SOIL_READING_BOUNDARY - 50);
        $updateCurrentReadingMessageDTO = new UpdateSensorCurrentReadingTransportMessageDTO(
            LDR::NAME,
            self::LDR_SENSOR_TO_UPDATE,
            [$humidCurrentReadingUpdateMessage],
            $sensor->getDevice()->getDeviceID()
        );

        $amqpMessage = new AMQPMessage(serialize($updateCurrentReadingMessageDTO));
        $result = $this->sut->execute($amqpMessage);

        /** @var \App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Analog $soilSensor */
        $standardReadingTypeRepository = $this->entityManager->getRepository(Analog::class);
        $ldrSensor = $standardReadingTypeRepository->findBySensorID($sensor->getSensorID())[0];

        self::assertTrue($result);
        self::assertNotEquals(
            $ldrSensor->getCurrentReading(),
            $humidCurrentReadingUpdateMessage->getCurrentReading()
        );
    }


    // DHT
    public function test_dht_current_reading_message_consumers_correctly_one_reading(): void
    {
        /** @var Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => self::DHT_SENSOR_TO_UPDATE]);

        $humidCurrentReadingUpdateMessage = new HumidityCurrentReadingUpdateRequestDTO(Humidity::HIGH_READING - 50);
        $updateCurrentReadingMessageDTO = new UpdateSensorCurrentReadingTransportMessageDTO(
            Dht::NAME,
            self::DHT_SENSOR_TO_UPDATE,
            [$humidCurrentReadingUpdateMessage],
            $sensor->getDevice()->getDeviceID()
        );

        $amqpMessage = new AMQPMessage(serialize($updateCurrentReadingMessageDTO));
        $result = $this->sut->execute($amqpMessage);

        /** @var Humidity $dhtSensor */
        $humidityRepository = $this->entityManager->getRepository(Humidity::class);
        $dhtSensor = $humidityRepository->findBySensorID($sensor->getSensorID())[0];

        self::assertTrue($result);
        self::assertEquals(
            $dhtSensor->getCurrentReading(),
            $humidCurrentReadingUpdateMessage->getCurrentReading()
        );
    }

    public function test_dht_current_reading_message_consumers_correctly_all_readings(): void
    {
        /** @var \App\Entity\Sensor\Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => self::DHT_SENSOR_TO_UPDATE]);

        $humidCurrentReadingUpdateMessage = new HumidityCurrentReadingUpdateRequestDTO(Humidity::HIGH_READING - 50);
        $tempCurrentReadingUpdateMessage = new TemperatureCurrentReadingUpdateRequestDTO(Dht::HIGH_TEMPERATURE_READING_BOUNDARY - 10);

        $updateCurrentReadingMessageDTO = new UpdateSensorCurrentReadingTransportMessageDTO(
            Dht::NAME,
            self::DHT_SENSOR_TO_UPDATE,
            [$tempCurrentReadingUpdateMessage, $humidCurrentReadingUpdateMessage],
            $sensor->getDevice()->getDeviceID()
        );

        $amqpMessage = new AMQPMessage(serialize($updateCurrentReadingMessageDTO));
        $result = $this->sut->execute($amqpMessage);

        /** @var Humidity $humidity */
        $humidityRepository = $this->entityManager->getRepository(Humidity::class);
        $humidity = $humidityRepository->findBySensorID($sensor->getSensorID())[0];

        /** @var \App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Temperature $temperature */
        $temperatureRepository = $this->entityManager->getRepository(Temperature::class);
        $temperature = $temperatureRepository->findBySensorID($sensor->getSensorID())[0];

        self::assertTrue($result);
        self::assertEquals(
            $humidity->getCurrentReading(),
            $humidCurrentReadingUpdateMessage->getCurrentReading()
        );
        self::assertEquals(
            $temperature->getCurrentReading(),
            $tempCurrentReadingUpdateMessage->getCurrentReading()
        );
    }

    public function test_dht_current_reading_message_consumers_wrong_current_reading_dto(): void
    {
        /** @var \App\Entity\Sensor\Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => self::DHT_SENSOR_TO_UPDATE]);

        $analogCurrentReadingUpdateMessage = new AnalogCurrentReadingUpdateRequestDTO(Humidity::HIGH_READING - 23);
        $updateCurrentReadingMessageDTO = new UpdateSensorCurrentReadingTransportMessageDTO(
            Dht::NAME,
            self::DHT_SENSOR_TO_UPDATE,
            [$analogCurrentReadingUpdateMessage],
            $sensor->getDevice()->getDeviceID()
        );

        $amqpMessage = new AMQPMessage(serialize($updateCurrentReadingMessageDTO));
        $result = $this->sut->execute($amqpMessage);

        /** @var Humidity $humidity */
        $humidityRepository = $this->entityManager->getRepository(Humidity::class);
        $humidity = $humidityRepository->findBySensorID($sensor->getSensorID())[0];

        /** @var Temperature $temperature */
        $temperatureRepository = $this->entityManager->getRepository(Temperature::class);
        $temperature = $temperatureRepository->findBySensorID($sensor->getSensorID())[0];

        self::assertTrue($result);
        self::assertNotEquals(
            $humidity->getCurrentReading(),
            $analogCurrentReadingUpdateMessage->getCurrentReading()
        );
        self::assertNotEquals(
            $temperature->getCurrentReading(),
            $analogCurrentReadingUpdateMessage->getCurrentReading()
        );
    }

    public function test_sht_current_reading_message_consumers_correctly_one_reading(): void
    {
        /** Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => self::SHT_SENSOR_TO_UPDATE]);

        $humidCurrentReadingUpdateMessage = new HumidityCurrentReadingUpdateRequestDTO(Humidity::HIGH_READING - 50);
        $updateCurrentReadingMessageDTO = new UpdateSensorCurrentReadingTransportMessageDTO(
            Sht::NAME,
            self::SHT_SENSOR_TO_UPDATE,
            [$humidCurrentReadingUpdateMessage],
            $sensor->getDevice()->getDeviceID()
        );

        $amqpMessage = new AMQPMessage(serialize($updateCurrentReadingMessageDTO));

        $result = $this->sut->execute($amqpMessage);

        /** @var Humidity $humidity */
        $humidityRepository = $this->entityManager->getRepository(Humidity::class);
        $humidity = $humidityRepository->findBySensorID($sensor->getSensorID())[0];

        self::assertTrue($result);
        self::assertEquals(
            $humidity->getCurrentReading(),
            $humidCurrentReadingUpdateMessage->getCurrentReading()
        );
    }

    public function test_sht_current_reading_message_consumers_correctly_all_readings(): void
    {
        /** Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => self::SHT_SENSOR_TO_UPDATE]);

        $humidCurrentReadingUpdateMessage = new HumidityCurrentReadingUpdateRequestDTO(Humidity::HIGH_READING - 50);
        $tempCurrentReadingUpdateMessage = new TemperatureCurrentReadingUpdateRequestDTO(Sht::HIGH_TEMPERATURE_READING_BOUNDARY - 10);

        $updateCurrentReadingMessageDTO = new UpdateSensorCurrentReadingTransportMessageDTO(
            Sht::NAME,
            self::SHT_SENSOR_TO_UPDATE,
            [$humidCurrentReadingUpdateMessage, $tempCurrentReadingUpdateMessage],
            $sensor->getDevice()->getDeviceID()
        );

        $amqpMessage = new AMQPMessage(serialize($updateCurrentReadingMessageDTO));

        $result = $this->sut->execute($amqpMessage);

        /** @var Humidity $humidity */
        $humidityRepository = $this->entityManager->getRepository(Humidity::class);
        $humidity = $humidityRepository->findBySensorID($sensor->getSensorID())[0];

        /** @var Temperature $temperature */
        $temperatureRepository = $this->entityManager->getRepository(Temperature::class);
        $temperature = $temperatureRepository->findBySensorID($sensor->getSensorID())[0];

        self::assertTrue($result);
        self::assertEquals(
            $humidity->getCurrentReading(),
            $humidCurrentReadingUpdateMessage->getCurrentReading()
        );
        self::assertEquals(
            $temperature->getCurrentReading(),
            $tempCurrentReadingUpdateMessage->getCurrentReading()
        );
    }

    public function test_sht_current_reading_message_consumers_wrong_current_reading_dto(): void
    {
        /** Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => self::SHT_SENSOR_TO_UPDATE]);

        $analogCurrentReadingUpdateMessage = new AnalogCurrentReadingUpdateRequestDTO(Humidity::HIGH_READING - 23);
        $updateCurrentReadingMessageDTO = new UpdateSensorCurrentReadingTransportMessageDTO(
            Sht::NAME,
            self::SHT_SENSOR_TO_UPDATE,
            [$analogCurrentReadingUpdateMessage],
            $sensor->getDevice()->getDeviceID()
        );

        $amqpMessage = new AMQPMessage(serialize($updateCurrentReadingMessageDTO));

        $result = $this->sut->execute($amqpMessage);

        /** @var Humidity $humidity */
        $humidityRepository = $this->entityManager->getRepository(Humidity::class);
        $humidity = $humidityRepository->findBySensorID($sensor->getSensorID())[0];

        /** @var Temperature $temperature */
        $temperatureRepository = $this->entityManager->getRepository(Temperature::class);
        $temperature = $temperatureRepository->findBySensorID($sensor->getSensorID())[0];

        self::assertTrue($result);
        self::assertNotEquals(
            $humidity->getCurrentReading(),
            $analogCurrentReadingUpdateMessage->getCurrentReading()
        );
        self::assertNotEquals(
            $temperature->getCurrentReading(),
            $analogCurrentReadingUpdateMessage->getCurrentReading()
        );
    }

    // Dallas
    public function test_dallas_current_reading_message_consumers_correctly_all_readings(): void
    {
        /** @var \App\Entity\Sensor\Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => self::DALLAS_SENSOR_TO_UPDATE]);

        $tempCurrentReadingUpdateMessage = new TemperatureCurrentReadingUpdateRequestDTO(Dallas::HIGH_TEMPERATURE_READING_BOUNDARY - 10);

        $updateCurrentReadingMessageDTO = new UpdateSensorCurrentReadingTransportMessageDTO(
            Dallas::NAME,
            self::DALLAS_SENSOR_TO_UPDATE,
            [$tempCurrentReadingUpdateMessage],
            $sensor->getDevice()->getDeviceID()
        );

        $amqpMessage = new AMQPMessage(serialize($updateCurrentReadingMessageDTO));
        $result = $this->sut->execute($amqpMessage);

        /** @var Temperature $temperature */
        $temperatureRepository = $this->entityManager->getRepository(Temperature::class);
        $temperature = $temperatureRepository->findBySensorID($sensor->getSensorID())[0];

        self::assertTrue($result);
        self::assertEquals(
            $temperature->getCurrentReading(),
            $tempCurrentReadingUpdateMessage->getCurrentReading()
        );
    }

    public function test_dallas_current_reading_message_consumers_wrong_current_reading_dto(): void
    {
        /** @var Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => self::DALLAS_SENSOR_TO_UPDATE]);

        $analogCurrentReadingUpdateMessage = new AnalogCurrentReadingUpdateRequestDTO(Humidity::HIGH_READING - 50);
        $updateCurrentReadingMessageDTO = new UpdateSensorCurrentReadingTransportMessageDTO(
            Dallas::NAME,
            self::DALLAS_SENSOR_TO_UPDATE,
            [$analogCurrentReadingUpdateMessage],
            $sensor->getDevice()->getDeviceID()
        );

        $amqpMessage = new AMQPMessage(serialize($updateCurrentReadingMessageDTO));
        $result = $this->sut->execute($amqpMessage);

        /** @var Temperature $temperature */
        $temperatureRepository = $this->entityManager->getRepository(Temperature::class);
        $temperature = $temperatureRepository->findBySensorID($sensor->getSensorID())[0];

        self::assertTrue($result);
        self::assertNotEquals(
            $temperature->getCurrentReading(),
            $analogCurrentReadingUpdateMessage->getCurrentReading()
        );
    }

    // BMP
    public function test_bmp_current_reading_message_consumers_correctly_one_reading(): void
    {
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => self::BMP_SENSOR_TO_UPDATE]);

        $latitudeCurrentReadingUpdateMessage = new LatitudeCurrentReadingUpdateRequestDTO(Latitude::HIGH_READING - 20);
        $updateCurrentReadingMessageDTO = new UpdateSensorCurrentReadingTransportMessageDTO(
            Bmp::NAME,
            self::BMP_SENSOR_TO_UPDATE,
            [$latitudeCurrentReadingUpdateMessage],
            $sensor->getDevice()->getDeviceID()
        );

        $amqpMessage = new AMQPMessage(serialize($updateCurrentReadingMessageDTO));
        $result = $this->sut->execute($amqpMessage);

        /** @var \App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Latitude $latitude */
        $latitudeRepository = $this->entityManager->getRepository(Latitude::class);
        $latitude = $latitudeRepository->findBySensorID($sensor->getSensorID())[0];

        self::assertTrue($result);
        self::assertEquals(
            $latitude->getCurrentReading(),
            $latitudeCurrentReadingUpdateMessage->getCurrentReading()
        );
    }

    public function test_bmp_current_reading_message_consumers_correctly_all_readings(): void
    {
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => self::BMP_SENSOR_TO_UPDATE]);

        $humidCurrentReadingUpdateMessage = new HumidityCurrentReadingUpdateRequestDTO(Humidity::HIGH_READING - 50);
        $tempCurrentReadingUpdateMessage = new TemperatureCurrentReadingUpdateRequestDTO(Dht::HIGH_TEMPERATURE_READING_BOUNDARY - 10);
        $latitudeCurrentReadingUpdateMessage = new LatitudeCurrentReadingUpdateRequestDTO(Latitude::HIGH_READING - 20);

        $updateCurrentReadingMessageDTO = new UpdateSensorCurrentReadingTransportMessageDTO(
            Bmp::NAME,
            self::BMP_SENSOR_TO_UPDATE,
            [$humidCurrentReadingUpdateMessage, $tempCurrentReadingUpdateMessage, $latitudeCurrentReadingUpdateMessage],
            $sensor->getDevice()->getDeviceID()
        );

        $amqpMessage = new AMQPMessage(serialize($updateCurrentReadingMessageDTO));
        $result = $this->sut->execute($amqpMessage);

        /** @var Humidity $humidity */
        $humidityRepository = $this->entityManager->getRepository(Humidity::class);
        $humidity = $humidityRepository->findBySensorID($sensor->getSensorID())[0];

        /** @var Temperature $temperature */
        $temperatureRepository = $this->entityManager->getRepository(Temperature::class);
        $temperature = $temperatureRepository->findBySensorID($sensor->getSensorID())[0];

        /** @var Latitude $latitude */
        $latitudeRepository = $this->entityManager->getRepository(Latitude::class);
        $latitude = $latitudeRepository->findBySensorID($sensor->getSensorID())[0];

        self::assertTrue($result);
        self::assertEquals(
            $humidity->getCurrentReading(),
            $humidCurrentReadingUpdateMessage->getCurrentReading()
        );
        self::assertEquals(
            $temperature->getCurrentReading(),
            $tempCurrentReadingUpdateMessage->getCurrentReading()
        );
        self::assertEquals(
            $latitude->getCurrentReading(),
            $tempCurrentReadingUpdateMessage->getCurrentReading()
        );
    }

    public function test_bmp_current_reading_message_consumers_wrong_current_reading_dto(): void
    {
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => self::BMP_SENSOR_TO_UPDATE]);

        $analogCurrentReadingUpdateMessage = new AnalogCurrentReadingUpdateRequestDTO(Humidity::HIGH_READING - 22);
        $updateCurrentReadingMessageDTO = new UpdateSensorCurrentReadingTransportMessageDTO(
            Bmp::NAME,
            self::BMP_SENSOR_TO_UPDATE,
            [$analogCurrentReadingUpdateMessage],
            $sensor->getDevice()->getDeviceID()
        );

        $amqpMessage = new AMQPMessage(serialize($updateCurrentReadingMessageDTO));
        $result = $this->sut->execute($amqpMessage);

        /** @var Humidity $humidity */
        $humidityRepository = $this->entityManager->getRepository(Humidity::class);
        $humidity = $humidityRepository->findBySensorID($sensor->getSensorID())[0];

        /** @var Temperature $temperature */
        $temperatureRepository = $this->entityManager->getRepository(Temperature::class);
        $temperature = $temperatureRepository->findBySensorID($sensor->getSensorID())[0];

        /** @var Latitude $latitude */
        $latitudeRepository = $this->entityManager->getRepository(Latitude::class);
        $latitude = $latitudeRepository->findBySensorID($sensor->getSensorID())[0];

        self::assertTrue($result);
        self::assertNotEquals(
            $humidity->getCurrentReading(),
            $analogCurrentReadingUpdateMessage->getCurrentReading()
        );
        self::assertNotEquals(
            $temperature->getCurrentReading(),
            $analogCurrentReadingUpdateMessage->getCurrentReading()
        );
        self::assertNotEquals(
            $latitude->getCurrentReading(),
            $analogCurrentReadingUpdateMessage->getCurrentReading()
        );
    }

    public function test_generic_motion_current_reading_message_consumers_correctly_all_readings(): void
    {
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => self::GENERIC_MOTION_SENSOR_TO_UPDATE]);

        $motionCurrentReadingUpdateMessage = new BoolCurrentReadingUpdateRequestDTO(false, Motion::READING_TYPE);
        $updateCurrentReadingMessageDTO = new UpdateSensorCurrentReadingTransportMessageDTO(
            GenericMotion::NAME,
            self::GENERIC_MOTION_SENSOR_TO_UPDATE,
            [$motionCurrentReadingUpdateMessage],
            $sensor->getDevice()->getDeviceID()
        );

        $amqpMessage = new AMQPMessage(serialize($updateCurrentReadingMessageDTO));
        $result = $this->sut->execute($amqpMessage);

        $motion = $this->entityManager->getRepository(Motion::class)->findBySensorID($sensor->getSensorID())[0];

        self::assertTrue($result);
        self::assertEquals(
            $motion->getCurrentReading(),
            $motionCurrentReadingUpdateMessage->getCurrentReading()
        );
    }

    public function test_generic_motion_current_reading_message_consumers_wrong_current_reading_dto(): void
    {
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => self::GENERIC_MOTION_SENSOR_TO_UPDATE]);

        $analogCurrentReadingUpdateMessage = new AnalogCurrentReadingUpdateRequestDTO(Humidity::HIGH_READING - 22);
        $updateCurrentReadingMessageDTO = new UpdateSensorCurrentReadingTransportMessageDTO(
            GenericMotion::NAME,
            self::GENERIC_MOTION_SENSOR_TO_UPDATE,
            [$analogCurrentReadingUpdateMessage],
            $sensor->getDevice()->getDeviceID()
        );

        $amqpMessage = new AMQPMessage(serialize($updateCurrentReadingMessageDTO));
        $result = $this->sut->execute($amqpMessage);

        $motion = $this->entityManager->getRepository(Motion::class)->findBySensorID($sensor->getSensorID())[0];

        self::assertTrue($result);
        self::assertNotSame(
            $motion->getCurrentReading(),
            $analogCurrentReadingUpdateMessage->getCurrentReading()
        );
    }


    public function test_generic_relay_current_reading_message_consumers_correctly_all_readings(): void
    {
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => self::GENERIC_RELAY_SENSOR_TO_UPDATE]);

        $relayCurrentReadingUpdateMessage = new BoolCurrentReadingUpdateRequestDTO(false, Relay::READING_TYPE);
        $updateCurrentReadingMessageDTO = new UpdateSensorCurrentReadingTransportMessageDTO(
            GenericRelay::NAME,
            self::GENERIC_RELAY_SENSOR_TO_UPDATE,
            [$relayCurrentReadingUpdateMessage],
            $sensor->getDevice()->getDeviceID()
        );

        $amqpMessage = new AMQPMessage(serialize($updateCurrentReadingMessageDTO));
        $result = $this->sut->execute($amqpMessage);

        $relay = $this->entityManager->getRepository(Relay::class)->findBySensorID($sensor->getSensorID())[0];

        self::assertTrue($result);
        self::assertEquals(
            $relay->getCurrentReading(),
            $relayCurrentReadingUpdateMessage->getCurrentReading()
        );
    }

    public function test_generic_relay_current_reading_message_consumers_wrong_current_reading_dto(): void
    {
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => self::GENERIC_RELAY_SENSOR_TO_UPDATE]);

        $analogCurrentReadingUpdateMessage = new AnalogCurrentReadingUpdateRequestDTO(Humidity::HIGH_READING - 22);
        $updateCurrentReadingMessageDTO = new UpdateSensorCurrentReadingTransportMessageDTO(
            GenericRelay::NAME,
            self::GENERIC_RELAY_SENSOR_TO_UPDATE,
            [$analogCurrentReadingUpdateMessage],
            $sensor->getDevice()->getDeviceID()
        );

        $amqpMessage = new AMQPMessage(serialize($updateCurrentReadingMessageDTO));
        $result = $this->sut->execute($amqpMessage);

        $relay = $this->entityManager->getRepository(Relay::class)->findBySensorID($sensor->getSensorID())[0];

        self::assertTrue($result);
        self::assertNotSame(
            $relay->getCurrentReading(),
            $analogCurrentReadingUpdateMessage->getCurrentReading()
        );
    }

}
