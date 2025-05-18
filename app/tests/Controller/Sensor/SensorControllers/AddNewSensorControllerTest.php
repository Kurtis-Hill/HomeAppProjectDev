<?php

namespace App\Tests\Controller\Sensor\SensorControllers;

use App\Controller\Sensor\SensorControllers\AddNewSensorController;
use App\DataFixtures\Core\UserDataFixtures;
use App\DataFixtures\ESP8266\ESP8266DeviceFixtures;
use App\Entity\Device\Devices;
use App\Entity\Sensor\AbstractSensorType;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\AbstractBoolReadingBaseSensor;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Motion;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Relay;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\AbstractStandardReadingType;
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
use App\Entity\Sensor\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Entity\Sensor\SensorTypes\Interfaces\AnalogReadingTypeInterface;
use App\Entity\Sensor\SensorTypes\Interfaces\HumidityReadingTypeInterface;
use App\Entity\Sensor\SensorTypes\Interfaces\LatitudeReadingTypeInterface;
use App\Entity\Sensor\SensorTypes\Interfaces\MotionSensorReadingTypeInterface;
use App\Entity\Sensor\SensorTypes\Interfaces\RelayReadingTypeInterface;
use App\Entity\Sensor\SensorTypes\Interfaces\SensorTypeInterface;
use App\Entity\Sensor\SensorTypes\Interfaces\TemperatureReadingTypeInterface;
use App\Entity\Sensor\SensorTypes\LDR;
use App\Entity\Sensor\SensorTypes\Sht;
use App\Entity\Sensor\SensorTypes\Soil;
use App\Entity\User\Group;
use App\Entity\User\User;
use App\Entity\UserInterface\Card\CardView;
use App\Repository\Device\ORM\DeviceRepository;
use App\Repository\User\ORM\GroupRepository;
use App\Services\API\APIErrorMessages;
use App\Services\API\HTTPStatusCodes;
use App\Services\Request\RequestTypeEnum;
use App\Tests\Controller\ControllerTestCase;
use Generator;
use Symfony\Component\HttpFoundation\Request;

class AddNewSensorControllerTest extends ControllerTestCase
{
    private const ADD_NEW_SENSOR_URL = '/HomeApp/api/user/sensor';

    private ?Devices $device;

    private DeviceRepository $deviceRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->deviceRepository = $this->entityManager->getRepository(Devices::class);
        $this->device = $this->entityManager->getRepository(Devices::class)->findOneBy(['deviceName' => ESP8266DeviceFixtures::LOGIN_TEST_ACCOUNT_NAME_ADMIN_GROUP_ONE['name']]);
    }

    public function newSensorSimpleDataProvider(): Generator
    {
        yield [
            'sensor' => Dht::class,
            'sensorName' => 'dhtTest'
        ];

        yield [
            'sensor' => Bmp::class,
            'sensorName' => 'bmpTest'
        ];

        yield [
            'sensor' => Soil::class,
            'sensorName' => 'soilTest'
        ];

        yield [
            'sensor' => Dallas::class,
            'sensorName' => 'dallasTest'
        ];

        yield [
            'sensor' => GenericMotion::class,
            'sensorName' => 'genericMotionTest'
        ];

        yield [
            'sensor' => GenericRelay::class,
            'sensorName' => 'genericRelayTest'
        ];

        yield [
            'sensor' => LDR::class,
            'sensorName' => 'ldrTest'
        ];

        yield [
            'sensor' => Sht::class,
            'sensorName' => 'shtTest'
        ];
    }

    public function newSensorExtendedDataProvider(): Generator
    {
        yield [
            'sensor' => Dht::class,
            'sensorName' => 'dhtTest',
            'class' => Dht::class,
            [
                'temperature' => Temperature::class,
                'humidity' => Humidity::class,
            ]
        ];

        yield [
            'sensor' => Bmp::class,
            'sensorName' => 'bmpTest',
            'class' => Bmp::class,
            [
                'temperature' => Temperature::class,
                'humidity' => Humidity::class,
                'latitude' => Latitude::class
            ]
        ];

        yield [
            'sensor' => Soil::class,
            'sensorName' => 'soilTest',
            'class' => Soil::class,
            [
                'analog' => Analog::class
            ]
        ];

        yield [
            'sensor' => Dallas::class,
            'sensorName' => 'dallasTest',
            'class' => Dallas::class,
            [
                'temperature' => Temperature::class,
            ]
        ];

        yield [
            'sensor' => GenericMotion::class,
            'sensorName' => 'genericMotionTest',
            'class' => GenericMotion::class,
            [
                'motion' => Motion::class
            ]
        ];

        yield [
            'sensor' => GenericRelay::class,
            'sensorName' => 'genericRelayTest',
            'class' => GenericRelay::class,
            [
                'relay' => Relay::class
            ]
        ];

        yield [
            'sensor' => LDR::class,
            'sensorName' => 'ldrTest',
            'class' => LDR::class,
            [
                'analog' => Analog::class
            ]
        ];

        yield [
            'sensor' => Sht::class,
            'sensorName' => 'shtTest',
            'class' => Sht::class,
            [
                'temperature' => Temperature::class,
                'humidity' => Humidity::class,
            ]
        ];
    }

    /**
     * @dataProvider newSensorSimpleDataProvider
     */
    public function test_can_add_new_sensor_correct_details(string $sensorType, string $sensorName): void
    {
        /** @var AbstractSensorType $sensorTypeObject */
        $sensorTypeObject = $this->entityManager->getRepository($sensorType)->findAll()[0];

        $devicePinsInUse = $this->deviceRepository->findAllDevicePinsInUse($this->device->getDeviceID());

        while (true) {
            $randomPin = random_int(0, 10);
            if (!in_array($randomPin, $devicePinsInUse)) {
                break;
            }
        }

        $readingInterval = 1000;
        $formData = [
            'sensorName' => $sensorName,
            'sensorTypeID' => $sensorTypeObject->getSensorTypeID(),
            'deviceID' => $this->device->getDeviceID(),
            'pinNumber' => $randomPin,
            'readingInterval' => $readingInterval,
        ];

        $this->authenticateAdminOne();
        $this->client->jsonRequest(
            Request::METHOD_POST,
            self::ADD_NEW_SENSOR_URL,
            $formData
        );
        self::assertResponseStatusCodeSame(HTTPStatusCodes::HTTP_CREATED);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $sensorID = $responseData['payload']['sensorID'];

        /** @var Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorID' => $sensorID]);

        self::assertInstanceOf(Sensor::class, $sensor);
        self::assertStringContainsString(AddNewSensorController::REQUEST_ACCEPTED_SUCCESS_CREATED, $responseData['title']);

        self::assertEquals($sensorName, $sensor->getSensorName());
        self::assertEquals($randomPin, $sensor->getPinNumber());
        self::assertEquals($readingInterval, $sensor->getReadingInterval());

        self::assertSensorIsSameAsExpected($sensor, $responseData['payload']);;
    }

    public function invalidSensorDataProvider(): Generator
    {
        yield 'special_characters_in_name' => [
            [
                'sensorName' => '&invalidName',
                'sensorTypeID' => Dht::class,
                'deviceID' => 1,
                'pinNumber' => 1,
                'readingInterval' => 1000,
            ],
            'The name cannot contain any special characters, please choose a different name',
            HTTPStatusCodes::HTTP_BAD_REQUEST,
        ];

        yield 'long_name' => [
            [
                'sensorName' => 'TestingTestingTestiLongName',
                'sensorTypeID' => Dht::class,
                'deviceID' => 1,
                'pinNumber' => 1,
                'readingInterval' => 1000,
            ],
            'Sensor name cannot be longer than 20 characters',
            HTTPStatusCodes::HTTP_BAD_REQUEST,
        ];

        yield 'short_name' => [
            [
                'sensorName' => 'T',
                'sensorTypeID' => Dht::class,
                'deviceID' => 1,
                'pinNumber' => 1,
                'readingInterval' => 1000,
            ],
            'Sensor name must be at least 2 characters long',
            HTTPStatusCodes::HTTP_BAD_REQUEST,
        ];

//        yield 'duplicate_name' => [
//            [
//                'sensorName' => 'existingSensorName',
//                'sensorTypeID' => Dht::class,
//                'deviceID' => 1,
//                'pinNumber' => 1,
//                'readingInterval' => 1000,
//            ],
//            sprintf(DuplicateSensorException::MESSAGE, 'existingSensorName'),
//            HTTPStatusCodes::HTTP_BAD_REQUEST,
//        ];

        yield 'non_existent_device_id' => [
            [
                'sensorName' => 'validName',
                'sensorTypeID' => Dht::class,
                'deviceID' => 999999,
                'pinNumber' => 1,
                'readingInterval' => 1000,
            ],
            'The device with ID "999999" does not exist.',
            HTTPStatusCodes::HTTP_BAD_REQUEST,
        ];

        yield 'non_existent_sensor_type' => [
            [
                'sensorName' => 'validName',
                'sensorTypeID' => 999999,
                'deviceID' => 1,
                'pinNumber' => 1,
                'readingInterval' => 1000,
            ],
            'Sensor type not found for id 999999',
            HTTPStatusCodes::HTTP_BAD_REQUEST,
        ];

        yield 'negative_pin_number' => [
            [
                'sensorName' => 'validName',
                'sensorTypeID' => Sht::class,
                'deviceID' => 1,
                'pinNumber' => -1,
                'readingInterval' => 1000,
            ],
            'pinNumber must be greater than -1',
            HTTPStatusCodes::HTTP_BAD_REQUEST,
        ];

        yield 'invalid_reading_interval_type' => [
            [
                'sensorName' => 'validName',
                'sensorTypeID' => Sht::class,
                'deviceID' => 1,
                'pinNumber' => 1,
                'readingInterval' => 'string',
            ],
            'This value should be of type int.',
            HTTPStatusCodes::HTTP_BAD_REQUEST,
        ];

        yield 'reading_interval_too_low' => [
            [
                'sensorName' => 'validName',
                'sensorTypeID' => Bmp::class,
                'deviceID' => 1,
                'pinNumber' => 1,
                'readingInterval' => Sensor::MIN_READING_INTERVAL - 5,
            ],
            'This value should be ' . Sensor::MIN_READING_INTERVAL . ' or more.',
            HTTPStatusCodes::HTTP_BAD_REQUEST,
        ];

        return [
            'negative_pin_number' => [
                [
                    'sensorName' => 'testing',
                    'sensorTypeID' => 1, // Replace with a valid sensor type ID
                    'deviceID' => 1, // Replace with a valid device ID
                    'pinNumber' => -1,
                    'readingInterval' => Sensor::DEFAULT_READING_INTERVAL,
                ],
                'pinNumber must be greater than -1',
                HTTPStatusCodes::HTTP_BAD_REQUEST,
            ],
            'invalid_reading_interval_type' => [
                [
                    'sensorName' => 'testing',
                    'sensorTypeID' => 1, // Replace with a valid sensor type ID
                    'deviceID' => 1, // Replace with a valid device ID
                    'pinNumber' => 10,
                    'readingInterval' => 'string',
                ],
                'This value should be of type int.',
                HTTPStatusCodes::HTTP_BAD_REQUEST,
            ],
            'reading_interval_too_low' => [
                [
                    'sensorName' => 'testing',
                    'sensorTypeID' => 1, // Replace with a valid sensor type ID
                    'deviceID' => 1, // Replace with a valid device ID
                    'pinNumber' => 10,
                    'readingInterval' => Sensor::MIN_READING_INTERVAL - 5,
                ],
                'This value should be ' . Sensor::MIN_READING_INTERVAL . ' or more.',
                HTTPStatusCodes::HTTP_BAD_REQUEST,
            ],
        ];
    }

    /**
     * @dataProvider invalidSensorDataProvider
     */
    public function test_add_new_sensor_with_invalid_data(array $formData, string $expectedError, int $expectedStatusCode): void
    {
        if (!is_int($formData['sensorTypeID'])) {
            /** @var AbstractSensorType $sensorType */
            $sensorType = $this->entityManager->getRepository($formData['sensorTypeID'])->findAll()[0];
            $formData['sensorTypeID'] = $sensorType->getSensorTypeID();
        }
        $this->authenticateAdminOne();

        $this->client->jsonRequest(
            Request::METHOD_POST,
            self::ADD_NEW_SENSOR_URL,
            $formData
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        self::assertEquals($expectedStatusCode, $this->client->getResponse()->getStatusCode());
        self::assertStringContainsString($expectedError, $responseData['errors'][array_key_first($responseData['errors'])]);
    }

//    /**
//     * @dataProvider newSensorSimpleDataProvider
//     * @param string $sensorTypeString
//     * @param string $sensorName
//     */
//    public function test_can_not_add_new_sensor_with_special_characters(string $sensorTypeString, string $sensorName): void
//    {
//        /** @var AbstractSensorType $sensorType */
//        $sensorType = $this->entityManager->getRepository($sensorTypeString)->findAll()[0];
//
//        $formData = [
//            'sensorName' => '&' . $sensorName,
//            'sensorTypeID' => $sensorType->getSensorTypeID(),
//            'deviceID' => $this->device->getDeviceID(),
//            'pinNumber' => 1,
//        ];
//
//        $this->authenticateAdminOne();
//        $this->client->jsonRequest(
//            Request::METHOD_POST,
//            self::ADD_NEW_SENSOR_URL,
//            $formData,
//        );
//
//        /** @var Sensor $sensor */
//        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => $formData['sensorName']]);
//
//        $responseData = json_decode($this->client->getResponse()->getContent(), true);
//
//        self::assertNull($sensor);
//        self::assertStringContainsString('The name cannot contain any special characters, please choose a different name', $responseData['errors']['sensorName']);
//        self::assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
//    }
//
//    /**
//     * @dataProvider newSensorSimpleDataProvider
//     * @param string $sensorTypeString
//     * @param string $sensorName
//     */
//    public function test_can_not_add_new_sensor_with_long_name(string $sensorTypeString, string $sensorName): void
//    {
//        /** @var AbstractSensorType $sensorType */
//        $sensorType = $this->entityManager->getRepository($sensorTypeString)->findAll()[0];
//
//        $formData = [
//            'sensorName' => 'TestingTestingTesti' . $sensorName,
//            'sensorTypeID' => $sensorType->getSensorTypeID(),
//            'deviceID' => $this->device->getDeviceID(),
//            'pinNumber' => 1,
//        ];
//
//        $this->authenticateAdminOne();
//        $this->client->jsonRequest(
//            Request::METHOD_POST,
//            self::ADD_NEW_SENSOR_URL,
//            $formData,
//        );
//
//        /** @var Sensor $sensor */
//        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => $formData['sensorName']]);
//        $responseData = json_decode($this->client->getResponse()->getContent(), true);
//
//        self::assertNull($sensor);
//        self::assertStringContainsString("Sensor name cannot be longer than 20 characters", $responseData['errors']['sensorName']);
//        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
//    }
//
//    /**
//     * @dataProvider newSensorSimpleDataProvider
//     */
//    public function test_can_not_add_new_sensor_with_short_name(string $sensorTypeString): void
//    {
//        /** @var AbstractSensorType $sensorType */
//        $sensorType = $this->entityManager->getRepository($sensorTypeString)->findAll()[0];
//
//        $formData = [
//            'sensorName' => 'T',
//            'sensorTypeID' => $sensorType->getSensorTypeID(),
//            'deviceID' => $this->device->getDeviceID(),
//            'pinNumber' => 1,
//        ];
//
//        $jsonData = json_encode($formData);
//
//        $this->client->request(
//            Request::METHOD_POST,
//            self::ADD_NEW_SENSOR_URL,
//            $formData,
//            [],
//            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
//            $jsonData,
//        );
//
//        /** @var Sensor $sensor */
//        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => $formData['sensorName']]);
//        $responseData = json_decode($this->client->getResponse()->getContent(), true);
//
//        self::assertNull($sensor);
//        self::assertStringContainsString('Sensor name must be at least 2 characters long', $responseData['errors']['sensorName']);
//        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
//    }

    /**
     * @dataProvider newSensorSimpleDataProvider
     */
    public function test_cannot_add_new_sensor_with_identical_name(string $sensorTypeString): void
    {
        /** @var Devices $device */
        $device = $this->entityManager->getRepository(Devices::class)->findOneBy(['deviceName' => ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES[ESP8266DeviceFixtures::ADMIN_USER_ONE_DEVICE_ADMIN_GROUP_ONE]['referenceName']]);
        /** @var AbstractSensorType $sensorType */
        $sensorType = $this->entityManager->getRepository($sensorTypeString)->findAll()[0];
        /** @var Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findBy(['deviceID' => $device->getDeviceID()])[0];

        $formData = [
            'sensorName' => $sensor->getSensorName(),
            'sensorTypeID' => $sensorType->getSensorTypeID(),
            'deviceID' => $device->getDeviceID(),
            'pinNumber' => 1,
            'readingInterval' => 1000,
        ];

        $this->authenticateAdminOne();
        $this->client->request(
            Request::METHOD_POST,
            self::ADD_NEW_SENSOR_URL,
            $formData,
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        self::assertStringContainsString(
            sprintf(
                'A sensor with the name "%s" already exists for the device with ID "1"',
                $sensor->getSensorName(),
            ),
            $responseData['errors'][0]
        );
        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider newSensorExtendedDataProvider
     */
    public function test_can_add_sensor_and_card_details_admin(string $sensorTypeString, string $sensorName, string $class, array $sensors): void
    {
        /** @var AbstractSensorType $sensorType */
        $sensorType = $this->entityManager->getRepository($sensorTypeString)->findAll()[0];

        $formData = [
            'sensorName' => $sensorName,
            'sensorTypeID' => $sensorType->getSensorTypeID(),
            'deviceID' => $this->device->getDeviceID(),
            'pinNumber' => 1,
            'readingInterval' => 1000,
        ];

        $this->authenticateAdminOne();
        $this->client->request(
            Request::METHOD_POST,
            self::ADD_NEW_SENSOR_URL,
            $formData
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $sensorID = $responseData['payload']['sensorID'];

        /** @var Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorID' => $sensorID]);

        /** @var SensorTypeInterface $sensorTypeObject */
        $sensorTypeObject = $this->entityManager->getRepository($class)->findAll()[0];
        /** @var CardView $cardView */
        $cardView = $this->entityManager->getRepository(CardView::class)->findOneBy(['sensor' => $sensorID]);

        foreach ($sensors as $sensorTypeClass) {
            $sensorType = $this->entityManager->getRepository($sensorTypeClass)->findBySensorID($sensor->getSensorID())[0];
            self::assertInstanceOf($sensorTypeClass, $sensorType);
        }

        self::assertInstanceOf(Sensor::class, $sensor);
        self::assertInstanceOf($class, $sensorTypeObject);
        self::assertInstanceOf(CardView::class, $cardView);

        self::assertEquals(HTTPStatusCodes::HTTP_CREATED, $this->client->getResponse()->getStatusCode());
        self::assertStringContainsString(AddNewSensorController::REQUEST_ACCEPTED_SUCCESS_CREATED, $responseData['title']);


        self::assertSensorIsSameAsExpected($sensor, $responseData['payload']);;
    }

    /**
     * @dataProvider newSensorExtendedDataProvider
     */
    public function test_can_add_sensor_and_card_details_admin_group_not_apart_of(string $sensorTypeString, string $sensorName, string $class, array $sensors): void
    {
        /** @var AbstractSensorType $sensorTypeObject */
        $sensorTypeObject = $this->entityManager->getRepository($sensorTypeString)->findAll()[0];
        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::ADMIN_USER_EMAIL_TWO]);
        /** @var GroupRepository $groupNameRepository */
        $groupNameRepository = $this->entityManager->getRepository(Group::class);
        /** @var Group[] $groupsNotApartOf */
        $groupsNotApartOf = $groupNameRepository->findGroupsUserIsNotApartOf(
            $user,
            $user->getAssociatedGroupIDs(),
        );

        $counter = 0;
        while (true) {
            $group = $groupsNotApartOf[$counter];
            $devices = $this->entityManager->getRepository(Devices::class)->findBy(['groupID' => $group->getGroupID()]);
            $counter++;
            if (!empty($devices)) {
                break;
            }
        }
        /** @var Devices[] $devices */
        $device = $devices[0];

        $devicePinsInUse = $this->deviceRepository->findAllDevicePinsInUse($device->getDeviceID());

        while (true) {
            $randomPin = random_int(0, 10);
            if (!in_array($randomPin, $devicePinsInUse)) {
                break;
            }
        }

        $formData = [
            'sensorName' => $sensorName . '1',
            'sensorTypeID' => $sensorTypeObject->getSensorTypeID(),
            'deviceID' => $device->getDeviceID(),
            'pinNumber' => $randomPin,
            'readingInterval' => Sensor::DEFAULT_READING_INTERVAL,
        ];

        $this->authenticateAdminOne();
        $this->client->request(
            Request::METHOD_POST,
            self::ADD_NEW_SENSOR_URL,
            $formData,
        );
        self::assertResponseStatusCodeSame(HTTPStatusCodes::HTTP_CREATED);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $payload = $responseData['payload'] ?? [];

        if (empty($payload)) {
            self::fail('Payload is empty');
        }
        $sensorID = $payload['sensorID'];

        /** @var Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorID' => $sensorID]);
//        /** @var SensorTypeInterface $sensorTypeObject */
//        $sensorTypeObject = $this->entityManager->getRepository($class)->findOneBy(['sensor' => $sensorID]);
        /** @var CardView $cardView */
        $cardView = $this->entityManager->getRepository(CardView::class)->findOneBy(['sensor' => $sensor]);

        $newReadingTypeObjects = array_merge(
            $this->entityManager->getRepository(AbstractStandardReadingType::class)->findBySensorID($sensor->getSensorID()),
            $this->entityManager->getRepository(AbstractBoolReadingBaseSensor::class)->findBySensorID($sensor->getSensorID()),
        );
        self::assertNotEmpty($newReadingTypeObjects);
        if ($sensorTypeObject instanceof SensorTypeInterface) {
            //            self::assertEquals($sensorTypeObject->getSensor()->getSensorID(), $sensor->getSensorID());
            self::assertEquals($sensorTypeObject::getSensorTypeName(), $sensor->getSensorTypeObject()::getSensorTypeName());
        }
        /** @var AllSensorReadingTypeInterface $sensorTypeClass */
        foreach ($newReadingTypeObjects as $sensorTypeClass) {
            /** @var AllSensorReadingTypeInterface $sensorType */
            $sensorType = $this->entityManager->getRepository($sensorTypeClass::class)->findBySensorID($sensor->getSensorID())[0];
            self::assertInstanceOf($sensorTypeClass::class, $sensorType);
            if ($sensorTypeClass instanceof TemperatureReadingTypeInterface) {
                self::assertEquals($sensorTypeObject->getMaxTemperature(), $sensorTypeClass->getHighReading());
                self::assertEquals($sensorTypeObject->getMinTemperature(), $sensorTypeClass->getLowReading());
            }
            if ($sensorTypeClass instanceof HumidityReadingTypeInterface) {
                self::assertEquals($sensorTypeObject->getMaxHumidity(), $sensorTypeClass->getHighReading());
                self::assertEquals($sensorTypeObject->getMinHumidity(), $sensorTypeClass->getLowReading());
            }
            if ($sensorTypeClass instanceof AnalogReadingTypeInterface) {
                self::assertEquals($sensorTypeObject->getMaxAnalog(), $sensorTypeClass->getHighReading());
                self::assertEquals($sensorTypeObject->getMinAnalog(), $sensorTypeClass->getLowReading());
            }
            if ($sensorTypeClass instanceof LatitudeReadingTypeInterface) {
                self::assertEquals($sensorTypeObject->getMaxLatitude(), $sensorTypeClass->getHighReading());
                self::assertEquals($sensorTypeObject->getMinLatitude(), $sensorTypeClass->getLowReading());
            }
            if ($sensorTypeClass instanceof MotionSensorReadingTypeInterface) {
                self::assertNull($sensorTypeClass->getExpectedReading());
                self::assertFalse($sensorTypeClass->getRequestedReading());
                self::assertFalse($sensorTypeClass->getCurrentReading());
            }
            if ($sensorTypeClass instanceof RelayReadingTypeInterface) {
                self::assertNull($sensorTypeClass->getExpectedReading());
                self::assertFalse($sensorTypeClass->getRequestedReading());
                self::assertFalse($sensorTypeClass->getCurrentReading());
            }
        }

        self::assertInstanceOf(Sensor::class, $sensor);
        self::assertInstanceOf($class, $sensorTypeObject);
        self::assertInstanceOf(CardView::class, $cardView);

        self::assertStringContainsString(AddNewSensorController::REQUEST_ACCEPTED_SUCCESS_CREATED, $responseData['title']);

        self::assertEquals(Sensor::DEFAULT_READING_INTERVAL, $sensor->getReadingInterval());
        self::assertSensorIsSameAsExpected($sensor, $responseData['payload']);;
    }

    /**
     * @dataProvider newSensorExtendedDataProvider
     */
    public function test_can_add_sensor_and_card_details_regular_user_admin_group_is_apart_of(string $sensorTypeString, string $sensorName, string $class, array $sensors): void
    {
        /** @var AbstractSensorType $sensorTypeMappingObject */
        $sensorTypeMappingObject = $this->entityManager->getRepository($sensorTypeString)->findAll()[0];


        $pinNumber = 1;

        $formData = [
            'sensorName' => $sensorName,
            'sensorTypeID' => $sensorTypeMappingObject->getSensorTypeID(),
            'deviceID' => $this->device->getDeviceID(),
            'pinNumber' => $pinNumber,
            'readingInterval' => Sensor::DEFAULT_READING_INTERVAL,
        ];

        $this->authenticateRegularUserTwo();
        $this->client->request(
            Request::METHOD_POST,
            self::ADD_NEW_SENSOR_URL. '?responseType=' . RequestTypeEnum::FULL->value,
            $formData,
        );
        self::assertEquals(HTTPStatusCodes::HTTP_CREATED, $this->client->getResponse()->getStatusCode());

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $sensorID = $responseData['payload']['sensorID'];

        /** @var Sensor $sensor */
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorID' => $sensorID]);
        /** @var SensorTypeInterface $sensorTypeObject */
        $sensorTypeObject = $this->entityManager->getRepository($class)->findAll()[0];
        /** @var CardView $cardView */
        $cardView = $this->entityManager->getRepository(CardView::class)->findOneBy(['sensor' => $sensorID]);

        foreach ($sensors as $sensorTypeClass) {
            /** @var AllSensorReadingTypeInterface $sensorType */
            $sensorType = $this->entityManager->getRepository($sensorTypeClass)->findBySensorID($sensor->getSensorID())[0];
            self::assertInstanceOf($sensorTypeClass, $sensorType);

            if ($sensorTypeObject instanceof TemperatureReadingTypeInterface && $sensorType instanceof Temperature) {
                self::assertEquals($sensorTypeObject->getMaxTemperature(), $sensorType->getHighReading());
                self::assertEquals($sensorTypeObject->getMinTemperature(), $sensorType->getLowReading());
            }
            if ($sensorTypeObject instanceof HumidityReadingTypeInterface && $sensorType instanceof Humidity) {
                self::assertEquals($sensorTypeObject->getMaxHumidity(), $sensorType->getHighReading());
                self::assertEquals($sensorTypeObject->getMinHumidity(), $sensorType->getLowReading());
            }
            if ($sensorTypeObject instanceof AnalogReadingTypeInterface && $sensorType instanceof Analog) {
                self::assertEquals($sensorTypeObject->getMaxAnalog(), $sensorType->getHighReading());
                self::assertEquals($sensorTypeObject->getMinAnalog(), $sensorType->getLowReading());
            }
            if ($sensorTypeObject instanceof LatitudeReadingTypeInterface && $sensorType instanceof Latitude) {
                self::assertEquals($sensorTypeObject->getMaxLatitude(), $sensorType->getHighReading());
                self::assertEquals($sensorTypeObject->getMinLatitude(), $sensorType->getLowReading());
            }
            if ($sensorTypeObject instanceof MotionSensorReadingTypeInterface && $sensorType instanceof Motion) {
                self::assertNull($sensorType->getExpectedReading());
                self::assertFalse($sensorType->getRequestedReading());
                self::assertFalse($sensorType->getCurrentReading());
            }
            if ($sensorTypeObject instanceof RelayReadingTypeInterface && $sensorType instanceof Relay) {
                self::assertNull($sensorType->getExpectedReading());
                self::assertFalse($sensorType->getRequestedReading());
                self::assertFalse($sensorType->getCurrentReading());
            }
        }

        self::assertInstanceOf(Sensor::class, $sensor);
        self::assertInstanceOf($class, $sensorTypeObject);
        self::assertInstanceOf(CardView::class, $cardView);

        self::assertEquals(Sensor::DEFAULT_READING_INTERVAL, $sensor->getReadingInterval());
        self::assertStringContainsString(AddNewSensorController::REQUEST_ACCEPTED_SUCCESS_CREATED, $responseData['title']);
        self::assertEquals($sensorName, $responseData['payload']['sensorName']);

        self::assertSensorIsSameAsExpected($sensor, $responseData['payload']);;
    }

    //disabled to allow for bus sensors, needs adjusting so that only bus sensors can be on same pin
//    public function test_adding_sensor_to_occupied_pin(): void
//    {
//        /** @var SensorType $sensorType */
//        $sensorType = $this->entityManager->getRepository(SensorType::class)->findOneBy(['sensorType' => Dht::NAME]);
//
//        $deviceToAddSensorToo = $this->deviceRepository->findOneBy(['deviceName' => ESP8266DeviceFixtures::ADMIN_USER_ONE_DEVICE_ADMIN_GROUP_ONE]);
//
//        $devicePinsInUse = $this->deviceRepository->findAllDevicePinsInUse($deviceToAddSensorToo->getDeviceID());
//
//        $formData = [
//            'sensorName' => 'testing',
//            'sensorTypeID' => $sensorType->getSensorTypeID(),
//            'deviceID' => $deviceToAddSensorToo->getDeviceID(),
//            'pinNumber' => $devicePinsInUse[0],
//        ];
//
//        $jsonData = json_encode($formData);
//
//        $this->client->request(
//            Request::METHOD_POST,
//            self::ADD_NEW_SENSOR_URL,
//            $formData,
//            [],
//            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
//            $jsonData
//        );
//
//        $responseData = json_decode($this->client->getResponse()->getContent(), true);
//
//        self::assertStringContainsString(sprintf('Sensor with pin %d already exists', $devicePinsInUse[0]), $responseData['errors'][0]);
//        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
//    }

//    /**
//     * @dataProvider invalidSensorDataProvider
//     */
//    public function test_invalid_sensor_data(array $formData, string $expectedError, int $expectedStatusCode): void
//    {
//        $this->authenticateAdminOne();
//        $this->client->jsonRequest(
//            Request::METHOD_POST,
//            self::ADD_NEW_SENSOR_URL,
//            $formData
//        );
//
//        $responseData = json_decode($this->client->getResponse()->getContent(), true);
//
//        self::assertStringContainsString($expectedError, $responseData['errors'][array_key_first($responseData['errors'])]);
//        self::assertEquals($expectedStatusCode, $this->client->getResponse()->getStatusCode());
//    }

//    public function invalidSensorDataProvider(): array
//    {
//        return [
//            'negative_pin_number' => [
//                [
//                    'sensorName' => 'testing',
//                    'sensorTypeID' => 1, // Replace with a valid sensor type ID
//                    'deviceID' => 1, // Replace with a valid device ID
//                    'pinNumber' => -1,
//                    'readingInterval' => Sensor::DEFAULT_READING_INTERVAL,
//                ],
//                'pinNumber must be greater than -1',
//                HTTPStatusCodes::HTTP_BAD_REQUEST,
//            ],
//            'invalid_reading_interval_type' => [
//                [
//                    'sensorName' => 'testing',
//                    'sensorTypeID' => 1, // Replace with a valid sensor type ID
//                    'deviceID' => 1, // Replace with a valid device ID
//                    'pinNumber' => 10,
//                    'readingInterval' => 'string',
//                ],
//                'This value should be of type int.',
//                HTTPStatusCodes::HTTP_BAD_REQUEST,
//            ],
//            'reading_interval_too_low' => [
//                [
//                    'sensorName' => 'testing',
//                    'sensorTypeID' => 1, // Replace with a valid sensor type ID
//                    'deviceID' => 1, // Replace with a valid device ID
//                    'pinNumber' => 10,
//                    'readingInterval' => Sensor::MIN_READING_INTERVAL - 5,
//                ],
//                'This value should be ' . Sensor::MIN_READING_INTERVAL . ' or more.',
//                HTTPStatusCodes::HTTP_BAD_REQUEST,
//            ],
//        ];
//    }

    /**
     * @dataProvider newSensorSimpleDataProvider
     */
    public function test_add_new_sensor_when_not_part_of_associated_device_group_regular_user(string $sensorTypeString, string $sensorName): void
    {
        /** @var AbstractSensorType $sensorType */
        $sensorType = $this->entityManager->getRepository($sensorTypeString)->findAll()[0];

        /** @var GroupRepository $groupRepository */
        $groupRepository = $this->entityManager->getRepository(Group::class);

        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_ONE]);
        /** @var Group[] $groupNames */
        $groupNames = $groupRepository->findGroupsUserIsNotApartOf(
            $user,
            $user->getAssociatedGroupIDs(),
        );

        if (empty($groupNames)) {
            self::fail('No groups found for user to add sensor to');
        }
        $groupName = $groupNames[0];

        /** @var Devices[] $devices */
        $devices = $this->entityManager->getRepository(Devices::class)->findBy(['groupID' => $groupName->getGroupID()]);

        $device = $devices[0];
        $formData = [
            'sensorName' => $sensorName,
            'sensorTypeID' => $sensorType->getSensorTypeID(),
            'deviceID' => $device->getDeviceID(),
            'pinNumber' => random_int(0, 10),
            'readingInterval' => Sensor::DEFAULT_READING_INTERVAL,
        ];

        $this->authenticateRegularUserOne();
        $this->client->jsonRequest(
            Request::METHOD_POST,
            self::ADD_NEW_SENSOR_URL,
            $formData,
        );

        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => $sensorName]);
        self::assertNull($sensor);
        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertStringContainsString('You Are Not Authorised To Be Here', $responseData['title']);
        self::assertStringContainsString(APIErrorMessages::ACCESS_DENIED, $responseData['errors'][0]);
        self::assertResponseStatusCodeSame(HTTPStatusCodes::HTTP_FORBIDDEN);
    }

//    /**
//     * @dataProvider wrongHttpsMethodDataProvider
//     */
//    public function test_using_wrong_http_method(string $httpVerb): void
//    {
//        $this->client->request(
//            $httpVerb,
//            self::ADD_NEW_SENSOR_URL,
//            [],
//            [],
//            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
//        );
//
//        self::assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $this->client->getResponse()->getStatusCode());
//    }

    public function wrongHttpsMethodDataProvider(): array
    {
        return [
            [Request::METHOD_GET],
            [Request::METHOD_PUT],
            [Request::METHOD_PATCH],
            [Request::METHOD_DELETE],
        ];
    }
}
