<?php

namespace Sensors\AMQP\Consumers;

use App\Doctrine\DataFixtures\ESP8266\SensorFixtures;
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

    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();
        $this->sut = $container->get(UploadCurrentReadingSensorDataConsumer::class);
        $this->entityManager = $container->get('doctrine.orm.default_entity_manager');
    }

    protected function tearDown(): void
    {
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
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => SensorFixtures::SENSORS[Soil::NAME]]);

        $analogCurrentReadingUpdateMessage = new AnalogCurrentReadingUpdateRequestDTO(Soil::HIGH_SOIL_READING_BOUNDARY - 50);
        $updateCurrentReadingMessageDTO = new UpdateSensorCurrentReadingMessageDTO(
            Soil::NAME,
            SensorFixtures::SENSORS[Soil::NAME],
            [$analogCurrentReadingUpdateMessage],
            $sensor->getDeviceObject()->getDeviceNameID()
        );

        $amqpMessage = new AMQPMessage(serialize($updateCurrentReadingMessageDTO));
        $result = $this->sut->execute($amqpMessage);

        $soilSensor = $this->entityManager->getRepository(Soil::class)->findOneBy(['sensorNameID' => $sensor->getSensorNameID()]);

        self::assertTrue($result);
        self::assertEquals(
            $soilSensor->getAnalogObject()->getCurrentReading(),
            $analogCurrentReadingUpdateMessage->getCurrentReading()
        );
    }

    public function test_soil_current_reading_message_consumers_wrong_current_reading_request_dto(): void
    {
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => SensorFixtures::SENSORS[Soil::NAME]]);

        $humidCurrentReadingUpdateMessage = new HumidityCurrentReadingUpdateRequestDTO(Soil::HIGH_SOIL_READING_BOUNDARY - 50);
        $updateCurrentReadingMessageDTO = new UpdateSensorCurrentReadingMessageDTO(
            Soil::NAME,
            SensorFixtures::SENSORS[Soil::NAME],
            [$humidCurrentReadingUpdateMessage],
            $sensor->getDeviceObject()->getDeviceNameID()
        );

        $amqpMessage = new AMQPMessage(serialize($updateCurrentReadingMessageDTO));
        $result = $this->sut->execute($amqpMessage);

        $soilSensor = $this->entityManager->getRepository(Soil::class)->findOneBy(['sensorNameID' => $sensor->getSensorNameID()]);

        self::assertTrue($result);
        self::assertNotEquals(
            $soilSensor->getAnalogObject()->getCurrentReading(),
            $humidCurrentReadingUpdateMessage->getCurrentReading()
        );
    }
    // DHT
    public function test_dht_current_reading_message_consumers_correctly_one_reading(): void
    {
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => SensorFixtures::SENSORS[Dht::NAME]]);

        $humidCurrentReadingUpdateMessage = new HumidityCurrentReadingUpdateRequestDTO(Humidity::HIGH_READING - 50);
        $updateCurrentReadingMessageDTO = new UpdateSensorCurrentReadingMessageDTO(
            Dht::NAME,
            SensorFixtures::SENSORS[Dht::NAME],
            [$humidCurrentReadingUpdateMessage],
            $sensor->getDeviceObject()->getDeviceNameID()
        );

        $amqpMessage = new AMQPMessage(serialize($updateCurrentReadingMessageDTO));
        $result = $this->sut->execute($amqpMessage);

        $dhtSensor = $this->entityManager->getRepository(Dht::class)->findOneBy(['sensorNameID' => $sensor->getSensorNameID()]);

        self::assertTrue($result);
        self::assertEquals(
            $dhtSensor->getHumidObject()->getCurrentReading(),
            $humidCurrentReadingUpdateMessage->getCurrentReading()
        );
    }

    public function test_dht_current_reading_message_consumers_correctly_all_readings(): void
    {
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => SensorFixtures::SENSORS[Dht::NAME]]);

        $humidCurrentReadingUpdateMessage = new HumidityCurrentReadingUpdateRequestDTO(Humidity::HIGH_READING - 50);
        $tempCurrentReadingUpdateMessage = new TemperatureCurrentReadingUpdateRequestDTO(Dht::HIGH_TEMPERATURE_READING_BOUNDARY - 10);

        $updateCurrentReadingMessageDTO = new UpdateSensorCurrentReadingMessageDTO(
            Dht::NAME,
            SensorFixtures::SENSORS[Dht::NAME],
            [$humidCurrentReadingUpdateMessage, $tempCurrentReadingUpdateMessage],
            $sensor->getDeviceObject()->getDeviceNameID()
        );

        $amqpMessage = new AMQPMessage(serialize($updateCurrentReadingMessageDTO));
        $result = $this->sut->execute($amqpMessage);

        $dhtSensor = $this->entityManager->getRepository(Dht::class)->findOneBy(['sensorNameID' => $sensor->getSensorNameID()]);

        self::assertTrue($result);
        self::assertEquals(
            $dhtSensor->getHumidObject()->getCurrentReading(),
            $humidCurrentReadingUpdateMessage->getCurrentReading()
        );
        self::assertEquals(
            $dhtSensor->getTempObject()->getCurrentReading(),
            $tempCurrentReadingUpdateMessage->getCurrentReading()
        );
    }

    public function test_dht_current_reading_message_consumers_wrong_current_reading_dto(): void
    {
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => SensorFixtures::SENSORS[Dht::NAME]]);

        $analogCurrentReadingUpdateMessage = new AnalogCurrentReadingUpdateRequestDTO(Humidity::HIGH_READING - 23);
        $updateCurrentReadingMessageDTO = new UpdateSensorCurrentReadingMessageDTO(
            Dht::NAME,
            SensorFixtures::SENSORS[Dht::NAME],
            [$analogCurrentReadingUpdateMessage],
            $sensor->getDeviceObject()->getDeviceNameID()
        );

        $amqpMessage = new AMQPMessage(serialize($updateCurrentReadingMessageDTO));
        $result = $this->sut->execute($amqpMessage);

        $dhtSensor = $this->entityManager->getRepository(Dht::class)->findOneBy(['sensorNameID' => $sensor->getSensorNameID()]);

        self::assertTrue($result);
        self::assertNotEquals(
            $dhtSensor->getHumidObject()->getCurrentReading(),
            $analogCurrentReadingUpdateMessage->getCurrentReading()
        );
        self::assertNotEquals(
            $dhtSensor->getTempObject()->getCurrentReading(),
            $analogCurrentReadingUpdateMessage->getCurrentReading()
        );
    }

    // Dallas
    public function test_dallas_current_reading_message_consumers_correctly_all_readings(): void
    {
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => SensorFixtures::SENSORS[Dallas::NAME]]);

        $tempCurrentReadingUpdateMessage = new TemperatureCurrentReadingUpdateRequestDTO(Dallas::HIGH_TEMPERATURE_READING_BOUNDARY - 10);

        $updateCurrentReadingMessageDTO = new UpdateSensorCurrentReadingMessageDTO(
            Dallas::NAME,
            SensorFixtures::SENSORS[Dallas::NAME],
            [$tempCurrentReadingUpdateMessage],
            $sensor->getDeviceObject()->getDeviceNameID()
        );

        $amqpMessage = new AMQPMessage(serialize($updateCurrentReadingMessageDTO));
        $result = $this->sut->execute($amqpMessage);

        $dallas = $this->entityManager->getRepository(Dallas::class)->findOneBy(['sensorNameID' => $sensor->getSensorNameID()]);

        self::assertTrue($result);
        self::assertEquals(
            $dallas->getTempObject()->getCurrentReading(),
            $tempCurrentReadingUpdateMessage->getCurrentReading()
        );
    }

    public function test_dallas_current_reading_message_consumers_wrong_current_reading_dto(): void
    {
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => SensorFixtures::SENSORS[Dallas::NAME]]);

        $analogCurrentReadingUpdateMessage = new AnalogCurrentReadingUpdateRequestDTO(Humidity::HIGH_READING - 50);
        $updateCurrentReadingMessageDTO = new UpdateSensorCurrentReadingMessageDTO(
            Dallas::NAME,
            SensorFixtures::SENSORS[Dallas::NAME],
            [$analogCurrentReadingUpdateMessage],
            $sensor->getDeviceObject()->getDeviceNameID()
        );

        $amqpMessage = new AMQPMessage(serialize($updateCurrentReadingMessageDTO));
        $result = $this->sut->execute($amqpMessage);

        $dallas = $this->entityManager->getRepository(Dallas::class)->findOneBy(['sensorNameID' => $sensor->getSensorNameID()]);

        self::assertTrue($result);
        self::assertNotEquals(
            $dallas->getTempObject()->getCurrentReading(),
            $analogCurrentReadingUpdateMessage->getCurrentReading()
        );
    }

    // BMP
    public function test_bmp_current_reading_message_consumers_correctly_one_reading(): void
    {
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => SensorFixtures::SENSORS[Bmp::NAME]]);

        $latitudeCurrentReadingUpdateMessage = new LatitudeCurrentReadingUpdateRequestDTO(Latitude::HIGH_READING - 20);
        $updateCurrentReadingMessageDTO = new UpdateSensorCurrentReadingMessageDTO(
            Bmp::NAME,
            SensorFixtures::SENSORS[Bmp::NAME],
            [$latitudeCurrentReadingUpdateMessage],
            $sensor->getDeviceObject()->getDeviceNameID()
        );

        $amqpMessage = new AMQPMessage(serialize($updateCurrentReadingMessageDTO));
        $result = $this->sut->execute($amqpMessage);

        $bmp = $this->entityManager->getRepository(Bmp::class)->findOneBy(['sensorNameID' => $sensor->getSensorNameID()]);

        self::assertTrue($result);
        self::assertEquals(
            $bmp->getLatitudeObject()->getCurrentReading(),
            $latitudeCurrentReadingUpdateMessage->getCurrentReading()
        );
    }

    public function test_bmp_current_reading_message_consumers_correctly_all_readings(): void
    {
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => SensorFixtures::SENSORS[Bmp::NAME]]);

        $humidCurrentReadingUpdateMessage = new HumidityCurrentReadingUpdateRequestDTO(Humidity::HIGH_READING - 50);
        $tempCurrentReadingUpdateMessage = new TemperatureCurrentReadingUpdateRequestDTO(Dht::HIGH_TEMPERATURE_READING_BOUNDARY - 10);
        $latitudeCurrentReadingUpdateMessage = new LatitudeCurrentReadingUpdateRequestDTO(Latitude::HIGH_READING - 20);

        $updateCurrentReadingMessageDTO = new UpdateSensorCurrentReadingMessageDTO(
            Bmp::NAME,
            SensorFixtures::SENSORS[Bmp::NAME],
            [$humidCurrentReadingUpdateMessage, $tempCurrentReadingUpdateMessage, $latitudeCurrentReadingUpdateMessage],
            $sensor->getDeviceObject()->getDeviceNameID()
        );

        $amqpMessage = new AMQPMessage(serialize($updateCurrentReadingMessageDTO));
        $result = $this->sut->execute($amqpMessage);

        $bmp = $this->entityManager->getRepository(Bmp::class)->findOneBy(['sensorNameID' => $sensor->getSensorNameID()]);

        self::assertTrue($result);
        self::assertEquals(
            $bmp->getHumidObject()->getCurrentReading(),
            $humidCurrentReadingUpdateMessage->getCurrentReading()
        );
        self::assertEquals(
            $bmp->getTempObject()->getCurrentReading(),
            $tempCurrentReadingUpdateMessage->getCurrentReading()
        );
        self::assertEquals(
            $bmp->getLatitudeObject()->getCurrentReading(),
            $tempCurrentReadingUpdateMessage->getCurrentReading()
        );
    }

    public function test_bmp_current_reading_message_consumers_wrong_current_reading_dto(): void
    {
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => SensorFixtures::SENSORS[Bmp::NAME]]);

        $analogCurrentReadingUpdateMessage = new AnalogCurrentReadingUpdateRequestDTO(Humidity::HIGH_READING - 22);
        $updateCurrentReadingMessageDTO = new UpdateSensorCurrentReadingMessageDTO(
            Bmp::NAME,
            SensorFixtures::SENSORS[Bmp::NAME],
            [$analogCurrentReadingUpdateMessage],
            $sensor->getDeviceObject()->getDeviceNameID()
        );

        $amqpMessage = new AMQPMessage(serialize($updateCurrentReadingMessageDTO));
        $result = $this->sut->execute($amqpMessage);

        $bmp = $this->entityManager->getRepository(Bmp::class)->findOneBy(['sensorNameID' => $sensor->getSensorNameID()]);

        self::assertTrue($result);
        self::assertNotEquals(
            $bmp->getHumidObject()->getCurrentReading(),
            $analogCurrentReadingUpdateMessage->getCurrentReading()
        );
        self::assertNotEquals(
            $bmp->getTempObject()->getCurrentReading(),
            $analogCurrentReadingUpdateMessage->getCurrentReading()
        );
        self::assertNotEquals(
            $bmp->getLatitudeObject()->getCurrentReading(),
            $analogCurrentReadingUpdateMessage->getCurrentReading()
        );
    }
}
