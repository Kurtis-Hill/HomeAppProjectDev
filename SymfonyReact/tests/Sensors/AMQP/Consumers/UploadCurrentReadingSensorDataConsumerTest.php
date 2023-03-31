<?php

namespace App\Tests\Sensors\AMQP\Consumers;

use App\ORM\DataFixtures\ESP8266\SensorFixtures;
use App\Devices\Entity\Devices;
use App\Sensors\AMQP\Consumers\UploadCurrentReadingSensorDataConsumer;
use App\Sensors\DTO\Internal\CurrentReadingDTO\AMQPDTOs\UpdateSensorCurrentReadingMessageDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\AnalogCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\HumidityCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\LatitudeCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\TemperatureCurrentReadingUpdateRequestDTO;
use App\Sensors\Entity\ReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\Latitude;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\Soil;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UploadCurrentReadingSensorDataConsumerTest extends KernelTestCase
{
    private UploadCurrentReadingSensorDataConsumer $sut;

    private ?EntityManagerInterface $entityManager;

    private const SOIL_SENSOR_TO_UPDATE = SensorFixtures::PERMISSION_CHECK_SENSORS["AdminUserOneDeviceAdminGroupOneSoil"]['sensorName'];

    private const DHT_SENSOR_TO_UPDATE = SensorFixtures::PERMISSION_CHECK_SENSORS["AdminUserOneDeviceAdminGroupOneDht"]['sensorName'];

    private const BMP_SENSOR_TO_UPDATE = SensorFixtures::PERMISSION_CHECK_SENSORS["AdminUserOneDeviceAdminGroupOneBmp"]['sensorName'];

    private const DALLAS_SENSOR_TO_UPDATE = SensorFixtures::PERMISSION_CHECK_SENSORS["AdminUserOneDeviceAdminGroupOneDallas"]['sensorName'];

    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();
        $this->sut = $container->get(UploadCurrentReadingSensorDataConsumer::class);
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

        $updateCurrentReadingMessageDTO = new UpdateSensorCurrentReadingMessageDTO(
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
        /** @var Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => self::SOIL_SENSOR_TO_UPDATE]);
        $analogCurrentReadingUpdateMessage = new AnalogCurrentReadingUpdateRequestDTO(Soil::HIGH_SOIL_READING_BOUNDARY - random_int(1, 50));
        $updateCurrentReadingMessageDTO = new UpdateSensorCurrentReadingMessageDTO(
            Soil::NAME,
            self::SOIL_SENSOR_TO_UPDATE,
            [$analogCurrentReadingUpdateMessage],
            $sensor->getDevice()->getDeviceID()
        );

        $amqpMessage = new AMQPMessage(serialize($updateCurrentReadingMessageDTO));
        $result = $this->sut->execute($amqpMessage);

        /** @var Soil $soilSensor */
        $soilSensor = $this->entityManager->getRepository(Soil::class)->findOneBy(['sensor' => $sensor->getSensorID()]);

        self::assertTrue($result);
        self::assertEquals(
            $analogCurrentReadingUpdateMessage->getCurrentReading(),
            $soilSensor->getAnalogObject()->getCurrentReading(),
        );
    }

    public function test_soil_current_reading_message_consumers_wrong_current_reading_request_dto(): void
    {
        /** @var Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => self::SOIL_SENSOR_TO_UPDATE]);

        $humidCurrentReadingUpdateMessage = new HumidityCurrentReadingUpdateRequestDTO(Soil::HIGH_SOIL_READING_BOUNDARY - 50);
        $updateCurrentReadingMessageDTO = new UpdateSensorCurrentReadingMessageDTO(
            Soil::NAME,
            self::SOIL_SENSOR_TO_UPDATE,
            [$humidCurrentReadingUpdateMessage],
            $sensor->getDevice()->getDeviceID()
        );

        $amqpMessage = new AMQPMessage(serialize($updateCurrentReadingMessageDTO));
        $result = $this->sut->execute($amqpMessage);

        /** @var Soil $soilSensor */
        $soilSensor = $this->entityManager->getRepository(Soil::class)->findOneBy(['sensor' => $sensor->getSensorID()]);

        self::assertTrue($result);
        self::assertNotEquals(
            $soilSensor->getAnalogObject()->getCurrentReading(),
            $humidCurrentReadingUpdateMessage->getCurrentReading()
        );
    }
    // DHT
    public function test_dht_current_reading_message_consumers_correctly_one_reading(): void
    {
        /** @var Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => self::DHT_SENSOR_TO_UPDATE]);

        $humidCurrentReadingUpdateMessage = new HumidityCurrentReadingUpdateRequestDTO(Humidity::HIGH_READING - 50);
        $updateCurrentReadingMessageDTO = new UpdateSensorCurrentReadingMessageDTO(
            Dht::NAME,
            self::DHT_SENSOR_TO_UPDATE,
            [$humidCurrentReadingUpdateMessage],
            $sensor->getDevice()->getDeviceID()
        );

        $amqpMessage = new AMQPMessage(serialize($updateCurrentReadingMessageDTO));
        $result = $this->sut->execute($amqpMessage);

        /** @var Dht $dhtSensor */
        $dhtSensor = $this->entityManager->getRepository(Dht::class)->findOneBy(['sensor' => $sensor->getSensorID()]);

        self::assertTrue($result);
        self::assertEquals(
            $dhtSensor->getHumidObject()->getCurrentReading(),
            $humidCurrentReadingUpdateMessage->getCurrentReading()
        );
    }

    public function test_dht_current_reading_message_consumers_correctly_all_readings(): void
    {
        /** @var Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => self::DHT_SENSOR_TO_UPDATE]);

        $humidCurrentReadingUpdateMessage = new HumidityCurrentReadingUpdateRequestDTO(Humidity::HIGH_READING - 50);
        $tempCurrentReadingUpdateMessage = new TemperatureCurrentReadingUpdateRequestDTO(Dht::HIGH_TEMPERATURE_READING_BOUNDARY - 10);

        $updateCurrentReadingMessageDTO = new UpdateSensorCurrentReadingMessageDTO(
            Dht::NAME,
            self::DHT_SENSOR_TO_UPDATE,
            [$humidCurrentReadingUpdateMessage, $tempCurrentReadingUpdateMessage],
            $sensor->getDevice()->getDeviceID()
        );

        $amqpMessage = new AMQPMessage(serialize($updateCurrentReadingMessageDTO));
        $result = $this->sut->execute($amqpMessage);

        /** @var Dht $dhtSensor */
        $dhtSensor = $this->entityManager->getRepository(Dht::class)->findOneBy(['sensor' => $sensor->getSensorID()]);

        self::assertTrue($result);
        self::assertEquals(
            $dhtSensor->getHumidObject()->getCurrentReading(),
            $humidCurrentReadingUpdateMessage->getCurrentReading()
        );
        self::assertEquals(
            $dhtSensor->getTemperature()->getCurrentReading(),
            $tempCurrentReadingUpdateMessage->getCurrentReading()
        );
    }

    public function test_dht_current_reading_message_consumers_wrong_current_reading_dto(): void
    {
        /** @var Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => self::DHT_SENSOR_TO_UPDATE]);

        $analogCurrentReadingUpdateMessage = new AnalogCurrentReadingUpdateRequestDTO(Humidity::HIGH_READING - 23);
        $updateCurrentReadingMessageDTO = new UpdateSensorCurrentReadingMessageDTO(
            Dht::NAME,
            self::DHT_SENSOR_TO_UPDATE,
            [$analogCurrentReadingUpdateMessage],
            $sensor->getDevice()->getDeviceID()
        );

        $amqpMessage = new AMQPMessage(serialize($updateCurrentReadingMessageDTO));
        $result = $this->sut->execute($amqpMessage);

        /** @var Dht $dhtSensor */
        $dhtSensor = $this->entityManager->getRepository(Dht::class)->findOneBy(['sensor' => $sensor->getSensorID()]);

        self::assertTrue($result);
        self::assertNotEquals(
            $dhtSensor->getHumidObject()->getCurrentReading(),
            $analogCurrentReadingUpdateMessage->getCurrentReading()
        );
        self::assertNotEquals(
            $dhtSensor->getTemperature()->getCurrentReading(),
            $analogCurrentReadingUpdateMessage->getCurrentReading()
        );
    }

    // Dallas
    public function test_dallas_current_reading_message_consumers_correctly_all_readings(): void
    {
        /** @var Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => self::DALLAS_SENSOR_TO_UPDATE]);

        $tempCurrentReadingUpdateMessage = new TemperatureCurrentReadingUpdateRequestDTO(Dallas::HIGH_TEMPERATURE_READING_BOUNDARY - 10);

        $updateCurrentReadingMessageDTO = new UpdateSensorCurrentReadingMessageDTO(
            Dallas::NAME,
            self::DALLAS_SENSOR_TO_UPDATE,
            [$tempCurrentReadingUpdateMessage],
            $sensor->getDevice()->getDeviceID()
        );

        $amqpMessage = new AMQPMessage(serialize($updateCurrentReadingMessageDTO));
        $result = $this->sut->execute($amqpMessage);

        /** @var Dallas $dallas */
        $dallas = $this->entityManager->getRepository(Dallas::class)->findOneBy(['sensor' => $sensor->getSensorID()]);

        self::assertTrue($result);
        self::assertEquals(
            $dallas->getTemperature()->getCurrentReading(),
            $tempCurrentReadingUpdateMessage->getCurrentReading()
        );
    }

    public function test_dallas_current_reading_message_consumers_wrong_current_reading_dto(): void
    {
        /** @var Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => self::DALLAS_SENSOR_TO_UPDATE]);

        $analogCurrentReadingUpdateMessage = new AnalogCurrentReadingUpdateRequestDTO(Humidity::HIGH_READING - 50);
        $updateCurrentReadingMessageDTO = new UpdateSensorCurrentReadingMessageDTO(
            Dallas::NAME,
            self::DALLAS_SENSOR_TO_UPDATE,
            [$analogCurrentReadingUpdateMessage],
            $sensor->getDevice()->getDeviceID()
        );

        $amqpMessage = new AMQPMessage(serialize($updateCurrentReadingMessageDTO));
        $result = $this->sut->execute($amqpMessage);

        /** @var Dallas $dallas */
        $dallas = $this->entityManager->getRepository(Dallas::class)->findOneBy(['sensor' => $sensor->getSensorID()]);

        self::assertTrue($result);
        self::assertNotEquals(
            $dallas->getTemperature()->getCurrentReading(),
            $analogCurrentReadingUpdateMessage->getCurrentReading()
        );
    }

    // BMP
    public function test_bmp_current_reading_message_consumers_correctly_one_reading(): void
    {
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => self::BMP_SENSOR_TO_UPDATE]);

        $latitudeCurrentReadingUpdateMessage = new LatitudeCurrentReadingUpdateRequestDTO(Latitude::HIGH_READING - 20);
        $updateCurrentReadingMessageDTO = new UpdateSensorCurrentReadingMessageDTO(
            Bmp::NAME,
            self::BMP_SENSOR_TO_UPDATE,
            [$latitudeCurrentReadingUpdateMessage],
            $sensor->getDevice()->getDeviceID()
        );

        $amqpMessage = new AMQPMessage(serialize($updateCurrentReadingMessageDTO));
        $result = $this->sut->execute($amqpMessage);

        $bmp = $this->entityManager->getRepository(Bmp::class)->findOneBy(['sensor' => $sensor->getSensorID()]);

        self::assertTrue($result);
        self::assertEquals(
            $bmp->getLatitudeObject()->getCurrentReading(),
            $latitudeCurrentReadingUpdateMessage->getCurrentReading()
        );
    }

    public function test_bmp_current_reading_message_consumers_correctly_all_readings(): void
    {
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => self::BMP_SENSOR_TO_UPDATE]);

        $humidCurrentReadingUpdateMessage = new HumidityCurrentReadingUpdateRequestDTO(Humidity::HIGH_READING - 50);
        $tempCurrentReadingUpdateMessage = new TemperatureCurrentReadingUpdateRequestDTO(Dht::HIGH_TEMPERATURE_READING_BOUNDARY - 10);
        $latitudeCurrentReadingUpdateMessage = new LatitudeCurrentReadingUpdateRequestDTO(Latitude::HIGH_READING - 20);

        $updateCurrentReadingMessageDTO = new UpdateSensorCurrentReadingMessageDTO(
            Bmp::NAME,
            self::BMP_SENSOR_TO_UPDATE,
            [$humidCurrentReadingUpdateMessage, $tempCurrentReadingUpdateMessage, $latitudeCurrentReadingUpdateMessage],
            $sensor->getDevice()->getDeviceID()
        );

        $amqpMessage = new AMQPMessage(serialize($updateCurrentReadingMessageDTO));
        $result = $this->sut->execute($amqpMessage);

        $bmp = $this->entityManager->getRepository(Bmp::class)->findOneBy(['sensor' => $sensor->getSensorID()]);

        self::assertTrue($result);
        self::assertEquals(
            $bmp->getHumidObject()->getCurrentReading(),
            $humidCurrentReadingUpdateMessage->getCurrentReading()
        );
        self::assertEquals(
            $bmp->getTemperature()->getCurrentReading(),
            $tempCurrentReadingUpdateMessage->getCurrentReading()
        );
        self::assertEquals(
            $bmp->getLatitudeObject()->getCurrentReading(),
            $tempCurrentReadingUpdateMessage->getCurrentReading()
        );
    }

    public function test_bmp_current_reading_message_consumers_wrong_current_reading_dto(): void
    {
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => self::BMP_SENSOR_TO_UPDATE]);

        $analogCurrentReadingUpdateMessage = new AnalogCurrentReadingUpdateRequestDTO(Humidity::HIGH_READING - 22);
        $updateCurrentReadingMessageDTO = new UpdateSensorCurrentReadingMessageDTO(
            Bmp::NAME,
            self::BMP_SENSOR_TO_UPDATE,
            [$analogCurrentReadingUpdateMessage],
            $sensor->getDevice()->getDeviceID()
        );

        $amqpMessage = new AMQPMessage(serialize($updateCurrentReadingMessageDTO));
        $result = $this->sut->execute($amqpMessage);

        $bmp = $this->entityManager->getRepository(Bmp::class)->findOneBy(['sensor' => $sensor->getSensorID()]);

        self::assertTrue($result);
        self::assertNotEquals(
            $bmp->getHumidObject()->getCurrentReading(),
            $analogCurrentReadingUpdateMessage->getCurrentReading()
        );
        self::assertNotEquals(
            $bmp->getTemperature()->getCurrentReading(),
            $analogCurrentReadingUpdateMessage->getCurrentReading()
        );
        self::assertNotEquals(
            $bmp->getLatitudeObject()->getCurrentReading(),
            $analogCurrentReadingUpdateMessage->getCurrentReading()
        );
    }
}
