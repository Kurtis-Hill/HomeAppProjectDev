<?php

namespace App\Tests\Sensors\AMQP\Consumers;

use App\Devices\Entity\Devices;
use App\ORM\DataFixtures\ESP8266\SensorFixtures;
use App\Sensors\AMQP\Consumers\ProcessCurrentReadingRequestConsumer;
use App\Sensors\DTO\Internal\CurrentReadingDTO\AMQPDTOs\UpdateSensorCurrentReadingMessageDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\AnalogCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\BoolCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\HumidityCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\LatitudeCurrentReadingUpdateRequestDTO;
use App\Sensors\DTO\Request\CurrentReadingRequest\ReadingTypes\TemperatureCurrentReadingUpdateRequestDTO;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Motion;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Relay;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\Latitude;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\GenericMotion;
use App\Sensors\Entity\SensorTypes\GenericRelay;
use App\Sensors\Entity\SensorTypes\LDR;
use App\Sensors\Entity\SensorTypes\Sht;
use App\Sensors\Entity\SensorTypes\Soil;
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

    //LDR
    public function test_ldr_current_reading_message_consumers_correctly(): void
    {
        /** @var Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => self::LDR_SENSOR_TO_UPDATE]);
        $analogCurrentReadingUpdateMessage = new AnalogCurrentReadingUpdateRequestDTO(LDR::HIGH_READING - random_int(1, 50));
        $updateCurrentReadingMessageDTO = new UpdateSensorCurrentReadingMessageDTO(
            LDR::NAME,
            self::LDR_SENSOR_TO_UPDATE,
            [$analogCurrentReadingUpdateMessage],
            $sensor->getDevice()->getDeviceID()
        );

        $amqpMessage = new AMQPMessage(serialize($updateCurrentReadingMessageDTO));
        $result = $this->sut->execute($amqpMessage);

        /** @var LDR $ldrSensor */
        $ldrSensor = $this->entityManager->getRepository(LDR::class)->findOneBy(['sensor' => $sensor->getSensorID()]);

        self::assertTrue($result);
        self::assertEquals(
            $analogCurrentReadingUpdateMessage->getCurrentReading(),
            $ldrSensor->getAnalogObject()->getCurrentReading(),
        );
    }

    public function test_ldr_current_reading_message_consumers_wrong_current_reading_request_dto(): void
    {
        /** @var Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => self::LDR_SENSOR_TO_UPDATE]);

        $humidCurrentReadingUpdateMessage = new HumidityCurrentReadingUpdateRequestDTO(Soil::HIGH_SOIL_READING_BOUNDARY - 50);
        $updateCurrentReadingMessageDTO = new UpdateSensorCurrentReadingMessageDTO(
            LDR::NAME,
            self::LDR_SENSOR_TO_UPDATE,
            [$humidCurrentReadingUpdateMessage],
            $sensor->getDevice()->getDeviceID()
        );

        $amqpMessage = new AMQPMessage(serialize($updateCurrentReadingMessageDTO));
        $result = $this->sut->execute($amqpMessage);

        /** @var LDR $ldrSensor */
        $ldrSensor = $this->entityManager->getRepository(LDR::class)->findOneBy(['sensor' => $sensor->getSensorID()]);

        self::assertTrue($result);
        self::assertNotEquals(
            $ldrSensor->getAnalogObject()->getCurrentReading(),
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

    public function test_sht_current_reading_message_consumers_correctly_one_reading(): void
    {
        /** Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => self::SHT_SENSOR_TO_UPDATE]);

        $humidCurrentReadingUpdateMessage = new HumidityCurrentReadingUpdateRequestDTO(Humidity::HIGH_READING - 50);
        $updateCurrentReadingMessageDTO = new UpdateSensorCurrentReadingMessageDTO(
            Sht::NAME,
            self::SHT_SENSOR_TO_UPDATE,
            [$humidCurrentReadingUpdateMessage],
            $sensor->getDevice()->getDeviceID()
        );

        $amqpMessage = new AMQPMessage(serialize($updateCurrentReadingMessageDTO));

        $result = $this->sut->execute($amqpMessage);

        /** @var Sht $shtSensor */
        $shtSensor = $this->entityManager->getRepository(Sht::class)->findOneBy(['sensor' => $sensor->getSensorID()]);

        self::assertTrue($result);
        self::assertEquals(
            $shtSensor->getHumidObject()->getCurrentReading(),
            $humidCurrentReadingUpdateMessage->getCurrentReading()
        );
    }

    public function test_sht_current_reading_message_consumers_correctly_all_readings(): void
    {
        /** Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => self::SHT_SENSOR_TO_UPDATE]);

        $humidCurrentReadingUpdateMessage = new HumidityCurrentReadingUpdateRequestDTO(Humidity::HIGH_READING - 50);
        $tempCurrentReadingUpdateMessage = new TemperatureCurrentReadingUpdateRequestDTO(Sht::HIGH_TEMPERATURE_READING_BOUNDARY - 10);

        $updateCurrentReadingMessageDTO = new UpdateSensorCurrentReadingMessageDTO(
            Sht::NAME,
            self::SHT_SENSOR_TO_UPDATE,
            [$humidCurrentReadingUpdateMessage, $tempCurrentReadingUpdateMessage],
            $sensor->getDevice()->getDeviceID()
        );

        $amqpMessage = new AMQPMessage(serialize($updateCurrentReadingMessageDTO));

        $result = $this->sut->execute($amqpMessage);

        /** @var Sht $shtSensor */
        $shtSensor = $this->entityManager->getRepository(Sht::class)->findOneBy(['sensor' => $sensor->getSensorID()]);

        self::assertTrue($result);
        self::assertEquals(
            $shtSensor->getHumidObject()->getCurrentReading(),
            $humidCurrentReadingUpdateMessage->getCurrentReading()
        );
        self::assertEquals(
            $shtSensor->getTemperature()->getCurrentReading(),
            $tempCurrentReadingUpdateMessage->getCurrentReading()
        );
    }

    public function test_sht_current_reading_message_consumers_wrong_current_reading_dto(): void
    {
        /** Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => self::SHT_SENSOR_TO_UPDATE]);

        $analogCurrentReadingUpdateMessage = new AnalogCurrentReadingUpdateRequestDTO(Humidity::HIGH_READING - 23);
        $updateCurrentReadingMessageDTO = new UpdateSensorCurrentReadingMessageDTO(
            Sht::NAME,
            self::SHT_SENSOR_TO_UPDATE,
            [$analogCurrentReadingUpdateMessage],
            $sensor->getDevice()->getDeviceID()
        );

        $amqpMessage = new AMQPMessage(serialize($updateCurrentReadingMessageDTO));

        $result = $this->sut->execute($amqpMessage);

        /** @var Sht $shtSensor */
        $shtSensor = $this->entityManager->getRepository(Sht::class)->findOneBy(['sensor' => $sensor->getSensorID()]);

        self::assertTrue($result);
        self::assertNotEquals(
            $shtSensor->getHumidObject()->getCurrentReading(),
            $analogCurrentReadingUpdateMessage->getCurrentReading()
        );
        self::assertNotEquals(
            $shtSensor->getTemperature()->getCurrentReading(),
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

    public function test_generic_motion_current_reading_message_consumers_correctly_all_readings(): void
    {
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => self::GENERIC_MOTION_SENSOR_TO_UPDATE]);

        $motionCurrentReadingUpdateMessage = new BoolCurrentReadingUpdateRequestDTO(false, Motion::READING_TYPE);
        $updateCurrentReadingMessageDTO = new UpdateSensorCurrentReadingMessageDTO(
            GenericMotion::NAME,
            self::GENERIC_MOTION_SENSOR_TO_UPDATE,
            [$motionCurrentReadingUpdateMessage],
            $sensor->getDevice()->getDeviceID()
        );

        $amqpMessage = new AMQPMessage(serialize($updateCurrentReadingMessageDTO));
        $result = $this->sut->execute($amqpMessage);

        $motion = $this->entityManager->getRepository(Motion::class)->findOneBy(['sensor' => $sensor->getSensorID()]);

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
        $updateCurrentReadingMessageDTO = new UpdateSensorCurrentReadingMessageDTO(
            GenericMotion::NAME,
            self::GENERIC_MOTION_SENSOR_TO_UPDATE,
            [$analogCurrentReadingUpdateMessage],
            $sensor->getDevice()->getDeviceID()
        );

        $amqpMessage = new AMQPMessage(serialize($updateCurrentReadingMessageDTO));
        $result = $this->sut->execute($amqpMessage);

        $motion = $this->entityManager->getRepository(Motion::class)->findOneBy(['sensor' => $sensor->getSensorID()]);

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
        $updateCurrentReadingMessageDTO = new UpdateSensorCurrentReadingMessageDTO(
            GenericRelay::NAME,
            self::GENERIC_RELAY_SENSOR_TO_UPDATE,
            [$relayCurrentReadingUpdateMessage],
            $sensor->getDevice()->getDeviceID()
        );

        $amqpMessage = new AMQPMessage(serialize($updateCurrentReadingMessageDTO));
        $result = $this->sut->execute($amqpMessage);

        $relay = $this->entityManager->getRepository(Relay::class)->findOneBy(['sensor' => $sensor->getSensorID()]);

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
        $updateCurrentReadingMessageDTO = new UpdateSensorCurrentReadingMessageDTO(
            GenericRelay::NAME,
            self::GENERIC_RELAY_SENSOR_TO_UPDATE,
            [$analogCurrentReadingUpdateMessage],
            $sensor->getDevice()->getDeviceID()
        );

        $amqpMessage = new AMQPMessage(serialize($updateCurrentReadingMessageDTO));
        $result = $this->sut->execute($amqpMessage);

        $relay = $this->entityManager->getRepository(Relay::class)->findOneBy(['sensor' => $sensor->getSensorID()]);

        self::assertTrue($result);
        self::assertNotSame(
            $relay->getCurrentReading(),
            $analogCurrentReadingUpdateMessage->getCurrentReading()
        );
    }

}
