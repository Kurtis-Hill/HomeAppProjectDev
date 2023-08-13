<?php

namespace App\Tests\Sensors\Controller\SensorControllers;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Common\Services\RequestQueryParameterHandler;
use App\Common\Services\RequestTypeEnum;
use App\Devices\Entity\Devices;
use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use App\ORM\DataFixtures\Core\UserDataFixtures;
use App\ORM\DataFixtures\ESP8266\ESP8266DeviceFixtures;
use App\Sensors\Controller\SensorControllers\UpdateSensorController;
use App\Sensors\Entity\Sensor;
use App\Sensors\Exceptions\DuplicateSensorException;
use App\Sensors\Repository\Sensors\SensorRepositoryInterface;
use App\Tests\Traits\TestLoginTrait;
use App\User\Entity\Group;
use App\User\Entity\User;
use App\User\Repository\ORM\GroupRepositoryInterface;
use App\User\Repository\ORM\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateSensorControllerTest extends WebTestCase
{
    use TestLoginTrait;

    private const UPDATE_SENSOR_URL = CommonURL::USER_HOMEAPP_API_URL . 'sensor/%d/update';

    private KernelBrowser $client;

    private ?string $userToken = null;

    private ?EntityManagerInterface $entityManager;

    private SensorRepositoryInterface $sensorRepository;

    private DeviceRepositoryInterface $deviceRepository;

    private UserRepositoryInterface $userRepository;

    private GroupRepositoryInterface $groupNameRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->sensorRepository = $this->entityManager->getRepository(Sensor::class);
        $this->deviceRepository = $this->entityManager->getRepository(Devices::class);
        $this->userRepository = $this->entityManager->getRepository(User::class);
        $this->groupNameRepository = $this->entityManager->getRepository(Group::class);

        $this->userToken = $this->setUserToken($this->client);
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
        parent::tearDown();
    }

    public function test_sending_wrong_format_should_return_bad_request(): void
    {
        /** @var Sensor[] $sensors */
        $sensors = $this->sensorRepository->findAll();

        $sensor = $sensors[0];

        $content = '?sensorName=Test Sensor&deviceID=Test Device';

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_SENSOR_URL, $sensor->getSensorID()),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
            $content,
        );

        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $title = $responseData['title'];
        $errorsPayload = $responseData['errors'];

        self::assertEquals(UpdateSensorController::BAD_REQUEST_NO_DATA_RETURNED, $title);
        self::assertEquals([APIErrorMessages::FORMAT_NOT_SUPPORTED], $errorsPayload);
    }


    /**
     * @dataProvider incorrectDataTypesDataProvider
     */
    public function test_sending_incorrect_data_types(mixed $sensorName, mixed $deviceID, mixed $pinNumber, mixed $readingInterval, array $errorMessage): void
    {
        /** @var Sensor[] $sensors */
        $sensors = $this->sensorRepository->findAll();

        $sensor = $sensors[0];

        $content = [
            'sensorName' => $sensorName,
            'deviceID' => $deviceID,
            'pinNumber' => $pinNumber,
            'readingInterval' => $readingInterval,
        ];

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_SENSOR_URL, $sensor->getSensorID()),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
            json_encode($content),
        );

        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $title = $responseData['title'];
        $errorsPayload = $responseData['errors'];

        self::assertEquals(UpdateSensorController::BAD_REQUEST_NO_DATA_RETURNED, $title);
        self::assertEquals($errorMessage, $errorsPayload);

        /** @var Sensor $sensorAfterUpdate */
        $sensorAfterUpdate = $this->sensorRepository->findOneBy(['sensorID' => $sensor->getSensorID()]);
        self::assertEquals($sensor->getSensorName(), $sensorAfterUpdate->getSensorName());
        self::assertEquals($sensor->getDevice(), $sensorAfterUpdate->getDevice());
    }

    public function incorrectDataTypesDataProvider(): Generator
    {
        yield [
            'sensorName' => [123],
            'deviceID' => 123,
            'pinNumber' => 1,
            'readingInterval' => 500,
            'errorMessage' => ['sensor name must be of type string|null you provided array'],
        ];

        yield [
            'sensorName' => 'sensor name',
            'deviceID' => [123],
            'pinNumber' => 1,
            'readingInterval' => 500,
            'errorMessage' => ['device must be of type int|null you provided array'],
        ];

        yield [
            'sensorName' => 123,
            'deviceID' => 123,
            'pinNumber' => 1,
            'readingInterval' => 500,
            'errorMessage' => ['sensor name must be of type string|null you provided 123'],
        ];

        yield [
            'sensorName' => ['sensor name'],
            'deviceID' => '123',
            'pinNumber' => 1,
            'readingInterval' => 500,
            'errorMessage' => [
                'sensor name must be of type string|null you provided array',
                'device must be of type int|null you provided "123"',
            ],
        ];

        yield [
            'sensorName' => 'sensorname',
            'deviceID' => 123,
            'pinNumber' => false,
            'readingInterval' => 500,
            'errorMessage' => ['pinNumber must be an int you have provided false'],
        ];

        yield [
            'sensorName' => 'sensorname',
            'deviceID' => 123,
            'pinNumber' => ['1'],
            'readingInterval' => 500,
            'errorMessage' => ['pinNumber must be an int you have provided array'],
        ];

        yield [
            'sensorName' => 'sensorname',
            'deviceID' => 123,
            'pinNumber' => 'string',
            'readingInterval' => 500,
            'errorMessage' => ['pinNumber must be an int you have provided "string"'],
        ];

        yield [
            'sensorName' => 'sensorname',
            'deviceID' => 123,
            'pinNumber' => 1,
            'readingInterval' => 'string',
            'errorMessage' => ['readingInterval must be a number'],
        ];

        yield [
            'sensorName' => 'sensorname',
            'deviceID' => 123,
            'pinNumber' => 1,
            'readingInterval' => ['string'],
            'errorMessage' => ['readingInterval must be a number'],
        ];

        yield [
            'sensorName' => 'sensorname',
            'deviceID' => 123,
            'pinNumber' => 1,
            'readingInterval' => false,
            'errorMessage' => ['readingInterval must be a number'],
        ];
    }

    public function test_admin_can_change_sensor_to_device_not_apart_of(): void
    {
        /** @var User $user */
        $user = $this->userRepository->findOneBy(['email' => UserDataFixtures::ADMIN_USER_EMAIL_TWO]);

        $userToken = $this->setUserToken($this->client, UserDataFixtures::ADMIN_USER_EMAIL_TWO);

        /** @var Group[] $groupUserIsApartOf */
        $groupUserIsApartOf = $this->groupNameRepository->findGroupsUserIsApartOf($user);

        if (empty($groupUserIsApartOf)) {
            self::fail('UserDTOs is not apart of any group');
        }
        foreach ($groupUserIsApartOf as $group) {
            $device = $this->deviceRepository->findOneBy(['groupID' => $group]);
            if ($device !== null) {
                break;
            }
        }

        if (!isset($device)) {
            self::fail('No device found for user');
        }

        /** @var Sensor[] $sensors */
        $sensors = $this->sensorRepository->findBy(['deviceID' => $device]);

        $sensorToUpdate = $sensors[0];

        /** @var Group[] $groupsUserIsNotApartOf */
        $groupsUserIsNotApartOf = $this->groupNameRepository->findGroupsUserIsNotApartOf($user);

        foreach ($groupsUserIsNotApartOf as $group) {
            $device = $this->deviceRepository->findOneBy(['groupID' => $group]);
            if ($device !== null) {
                break;
            }
        }
        $deviceID = $device->getDeviceID();
        $newSensorName = 'newName';

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_SENSOR_URL, $sensorToUpdate->getSensorID()),
            [RequestQueryParameterHandler::RESPONSE_TYPE => RequestTypeEnum::FULL->value],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $userToken, 'CONTENT_TYPE' => 'application/json'],
            json_encode(['deviceID' => $deviceID, 'sensorName' => $newSensorName, 'pinNumber' => 10]),
        );

//        dd($this->client->getResponse()->getContent());
        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
        self::assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);

        $payload = $responseData['payload'];
        self::assertEquals($sensorToUpdate->getSensorID(), $payload['sensorID']);
        self::assertEquals($newSensorName, $payload['sensorName']);
        self::assertEquals($device->getDeviceName(), $payload['device']['deviceName']);
        self::assertEquals($sensorToUpdate->getSensorTypeObject()->getSensorType(), $payload['sensorType']['sensorTypeName']);
        self::assertEquals($sensorToUpdate->getCreatedBy()->getEmail(), $payload['createdBy']['email']);

//        /** @var Sensor $sensorAfterUpdate */
//        $sensorAfterUpdate = $this->sensorRepository->find($sensorToUpdate->getSensorID());
//        self::assertEquals($newSensorName, $sensorAfterUpdate->getSensorName());
//        self::assertEquals($deviceId, $sensorAfterUpdate->getDevice()->getDeviceID());
    }

    public function test_user_cannot_change_sensor_to_group_not_apart_of(): void
    {
        /** @var User $user */
        $user = $this->userRepository->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_TWO]);

        $userToken = $this->setUserToken($this->client, UserDataFixtures::REGULAR_USER_EMAIL_TWO, UserDataFixtures::REGULAR_PASSWORD);

        /** @var Group[] $groupUserIsApartOf */
        $groupUserIsApartOf = $this->groupNameRepository->findGroupsUserIsApartOf($user);

        if (empty($groupUserIsApartOf)) {
            self::fail('UserDTOs is not apart of any group');
        }
        foreach ($groupUserIsApartOf as $group) {
            $device = $this->deviceRepository->findOneBy(['groupID' => $group]);
            if ($device !== null) {
                break;
            }
        }

        if (!isset($device)) {
            self::fail('No device found for user');
        }

        /** @var Sensor[] $sensors */
        $sensors = $this->sensorRepository->findBy(['deviceID' => $device]);

        $sensorToUpdate = $sensors[0];

        /** @var Group[] $groupsUserIsNotApartOf */
        $groupsUserIsNotApartOf = $this->groupNameRepository->findGroupsUserIsNotApartOf($user);

        foreach ($groupsUserIsNotApartOf as $group) {
            $device = $this->deviceRepository->findOneBy(['groupID' => $group]);
            if ($device !== null) {
                break;
            }
        }
        if (!isset($device)) {
            self::fail('No device found for user');
        }
        $deviceId = $device->getDeviceID();
        $newSensorName = 'newName';

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_SENSOR_URL, $sensorToUpdate->getSensorID()),
            [RequestQueryParameterHandler::RESPONSE_TYPE => RequestTypeEnum::FULL->value],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $userToken, 'CONTENT_TYPE' => 'application/json'],
            json_encode(['deviceID' => $deviceId, 'sensorName' => $newSensorName]),
        );

        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        $title = $responseData['title'];
        $errorsPayload = $responseData['errors'];
        self::assertEquals(UpdateSensorController::NOT_AUTHORIZED_TO_BE_HERE, $title);
        self::assertEquals([APIErrorMessages::ACCESS_DENIED], $errorsPayload);

        /** @var Sensor $sensorAfterUpdate */
        $sensorAfterUpdate = $this->sensorRepository->findOneBy(['sensorID' => $sensorToUpdate->getSensorID()]);

        self::assertEquals($sensorToUpdate->getSensorName(), $sensorAfterUpdate->getSensorName());
        self::assertEquals($sensorToUpdate->getDevice()->getDeviceID(), $sensorAfterUpdate->getDevice()->getDeviceID());
        self::assertEquals($sensorToUpdate->getPinNumber(), $sensorAfterUpdate->getPinNumber());
    }

    public function test_just_updating_device_id(): void
    {
        $sensors = $this->sensorRepository->findAll();

        /** @var Sensor $sensorToUpdate */
        $sensorToUpdate = $sensors[0];

        $user = $this->userRepository->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_ONE]);

        /** @var Group[] $groupUserIsApartOf */
        $groupUserIsApartOf = $this->groupNameRepository->findGroupsUserIsApartOf($user);

        if (empty($groupUserIsApartOf)) {
            self::fail('UserDTOs is not apart of any group');
        }
        foreach ($groupUserIsApartOf as $group) {
            $device = $this->deviceRepository->findOneBy(['groupID' => $group]);
            if ($device !== null) {
                break;
            }
        }

        if (!isset($device)) {
            self::fail('No device found for user');
        }

        $deviceID = $device->getDeviceID();
        $pinNumber = 10;

        //have to assign pin number to avoid clashing with other fixtures
        $this->client->request(
            Request::METHOD_PATCH,
            sprintf(self::UPDATE_SENSOR_URL, $sensorToUpdate->getSensorID()),
            [RequestQueryParameterHandler::RESPONSE_TYPE => RequestTypeEnum::FULL->value],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
            json_encode(['deviceID' => $deviceID, 'pinNumber' => $pinNumber]),
        );
        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
        self::assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);

        $payload = $responseData['payload'];

        /** @var Sensor $sensorAfterUpdate */
        $sensorAfterUpdate = $this->sensorRepository->findOneBy(['sensorID' => $sensorToUpdate->getSensorID()]);
        self::assertEquals($sensorAfterUpdate->getDevice()->getDeviceID(), $deviceID);

        self::assertEquals($sensorToUpdate->getSensorID(), $payload['sensorID']);
        self::assertEquals($sensorToUpdate->getSensorName(), $payload['sensorName']);
        self::assertEquals($device->getDeviceName(), $payload['device']['deviceName']);
//        self::assertEquals($sensorToUpdate->getSensorTypeObject()->getSensorType(), $payload['sensorType']['sensorTypeName']);
        self::assertEquals($sensorToUpdate->getCreatedBy()->getEmail(), $payload['createdBy']['email']);
        self::assertEquals($pinNumber, $payload['pinNumber']);
    }

    public function test_just_updating_sensor_name(): void
    {
        $sensors = $this->sensorRepository->findAll();

        /** @var Sensor $sensorToUpdate */
        $sensorToUpdate = $sensors[0];

        $newSensorName = 'newName';

        $this->client->request(
            Request::METHOD_PATCH,
            sprintf(self::UPDATE_SENSOR_URL, $sensorToUpdate->getSensorID()),
            [RequestQueryParameterHandler::RESPONSE_TYPE => RequestTypeEnum::FULL->value],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
            json_encode(['sensorName' => $newSensorName]),
        );

        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
        self::assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);

        $payload = $responseData['payload'];

        self::assertEquals($sensorToUpdate->getSensorID(), $payload['sensorID']);
        self::assertEquals($newSensorName, $payload['sensorName']);
        self::assertEquals($sensorToUpdate->getDevice()->getDeviceName(), $payload['device']['deviceName']);
        self::assertEquals($sensorToUpdate->getSensorTypeObject()->getSensorType(), $payload['sensorType']['sensorTypeName']);
        self::assertEquals($sensorToUpdate->getCreatedBy()->getEmail(), $payload['createdBy']['email']);
        self::assertEquals($sensorToUpdate->getPinNumber(), $payload['pinNumber']);
    }

    public function test_updating_just_pin_number(): void
    {
        /** @var Devices $device */
        $device = $this->deviceRepository->findOneBy(['deviceName' => ESP8266DeviceFixtures::ADMIN_USER_ONE_DEVICE_ADMIN_GROUP_ONE]);

        $devicePinsInUse = $this->deviceRepository->findAllDevicePinsInUse($device->getDeviceID());

        while (true) {
            $randomPin = random_int(0, 10);
            if (!in_array($randomPin, $devicePinsInUse)) {
                break;
            }
        }

        /** @var Sensor $sensor */
        $sensor = $this->sensorRepository->findOneBy(['deviceID' => $device->getDeviceID()]);

        $this->client->request(
            Request::METHOD_PATCH,
            sprintf(self::UPDATE_SENSOR_URL, $sensor->getSensorID()),
            [RequestQueryParameterHandler::RESPONSE_TYPE => RequestTypeEnum::FULL->value],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
            json_encode(['pinNumber' => $randomPin]),
        );

        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);

        $payload = $responseData['payload'];

        self::assertEquals($sensor->getSensorID(), $payload['sensorID']);
        self::assertEquals($sensor->getSensorName(), $payload['sensorName']);
        self::assertEquals($device->getDeviceName(), $payload['device']['deviceName']);
        self::assertEquals($sensor->getSensorTypeObject()->getSensorType(), $payload['sensorType']['sensorTypeName']);
        self::assertEquals($sensor->getCreatedBy()->getEmail(), $payload['createdBy']['email']);
        self::assertEquals($randomPin, $payload['pinNumber']);
    }

//    public function test_updating_pin_to_pin_that_is_already_registered_to_device(): void
//    {
//        /** @var Devices $device */
//        $device = $this->deviceRepository->findOneBy(['deviceName' => ESP8266DeviceFixtures::ADMIN_USER_ONE_DEVICE_ADMIN_GROUP_ONE]);
//
//        $devicePinsInUse = $this->deviceRepository->findAllDevicePinsInUse($device->getDeviceID());
//
//        $randomPin = $devicePinsInUse[1];
//
////        dd($randomPin);
//
//        /** @var Sensor $sensor */
//        $sensor = $this->sensorRepository->findOneBy(['deviceID' => $device->getDeviceID()]);
//
//        $this->client->request(
//            Request::METHOD_PATCH,
//            sprintf(self::UPDATE_SENSOR_URL, $sensor->getSensorID()),
//            [RequestQueryParameterHandler::RESPONSE_TYPE => RequestTypeEnum::FULL->value],
//            [],
//            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
//            json_encode(['pinNumber' => $randomPin]),
//        );
//
//        $responseData = json_decode(
//            $this->client->getResponse()->getContent(),
//            true,
//            512,
//            JSON_THROW_ON_ERROR
//        );
//
//        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
//
//        $title = $responseData['title'];
//        $errorsPayload = $responseData['errors'];
//
//        self::assertEquals(UpdateSensorController::BAD_REQUEST_NO_DATA_RETURNED, $title);
//
//        self::assertEquals([
//            sprintf(
//                DuplicateSensorException::MESSAGE_PIN,
//                $randomPin,
//                $sensor->getSensorName()
//            )
//        ], $errorsPayload);
//    }

    public function test_updating_just_reading_interval(): void
    {
        /** @var Sensor $sensor */
        $sensor = $this->sensorRepository->findAll()[0];

        $newReadingInterval = 1000;

        $this->client->request(
            Request::METHOD_PATCH,
            sprintf(self::UPDATE_SENSOR_URL, $sensor->getSensorID()),
            [RequestQueryParameterHandler::RESPONSE_TYPE => RequestTypeEnum::FULL->value],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
            json_encode(['readingInterval' => $newReadingInterval]),
        );

        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);

        $payload = $responseData['payload'];

        self::assertEquals($sensor->getSensorID(), $payload['sensorID']);
        self::assertEquals($sensor->getSensorName(), $payload['sensorName']);
        self::assertEquals($sensor->getDevice()->getDeviceName(), $payload['device']['deviceName']);
        self::assertEquals($sensor->getSensorTypeObject()->getSensorType(), $payload['sensorType']['sensorTypeName']);
        self::assertEquals($sensor->getCreatedBy()->getEmail(), $payload['createdBy']['email']);
        self::assertEquals($sensor->getPinNumber(), $payload['pinNumber']);
        self::assertEquals($newReadingInterval, $payload['readingInterval']);

        $sensorAfterUpdate = $this->sensorRepository->findOneBy(['sensorID' => $sensor->getSensorID()]);

        self::assertEquals($newReadingInterval, $sensorAfterUpdate->getReadingInterval());
    }

    public function test_updating_sensor_correct_data_regular_user(): void
    {
        $sensors = $this->sensorRepository->findAll();

        /** @var User $user */
        $user = $this->userRepository->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_ONE]);
        foreach ($sensors as $sensor) {
            if (
                in_array(
                    $sensor->getDevice()->getGroupObject()->getGroupID(),
                    $user->getAssociatedGroupIDs(),
                    true
                )) {
                /** @var Sensor $sensorToUpdate */
                $sensorToUpdate = $sensor;
                break;
            }
        }

        if (!isset($sensorToUpdate)) {
            self::fail('No sensor found for user');
        }

        $userToken = $this->setUserToken($this->client, UserDataFixtures::REGULAR_USER_EMAIL_ONE, UserDataFixtures::REGULAR_PASSWORD);

        /** @var Group[] $groupUserIsApartOf */
        $groupUserIsApartOf = $this->groupNameRepository->findGroupsUserIsApartOf($user);

        if (empty($groupUserIsApartOf)) {
            self::fail('UserDTOs is not apart of any group');
        }
        foreach ($groupUserIsApartOf as $group) {
            $device = $this->deviceRepository->findOneBy(['groupID' => $group]);
            if ($device !== null) {
                break;
            }
        }

        if (!isset($device)) {
            self::fail('No device found for user');
        }

        $deviceId = $device->getDeviceID();
        $newSensorName = 'newName';

        $this->client->request(
            Request::METHOD_PATCH,
            sprintf(self::UPDATE_SENSOR_URL, $sensorToUpdate->getSensorID()),
            [RequestQueryParameterHandler::RESPONSE_TYPE => RequestTypeEnum::FULL->value],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $userToken, 'CONTENT_TYPE' => 'application/json'],
            json_encode(['deviceID' => $deviceId, 'sensorName' => $newSensorName]),
        );

        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
        self::assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);

        $payload = $responseData['payload'];
        self::assertEquals($sensorToUpdate->getSensorID(), $payload['sensorID']);
        self::assertEquals($newSensorName, $payload['sensorName']);
        self::assertEquals($device->getDeviceName(), $payload['device']['deviceName']);
        self::assertEquals($sensorToUpdate->getSensorTypeObject()->getSensorType(), $payload['sensorType']['sensorTypeName']);

        /** @var Sensor $sensorAfterUpdate */
        $sensorAfterUpdate = $this->sensorRepository->findOneBy(['sensorID' => $sensorToUpdate->getSensorID()]);
        self::assertEquals($sensorAfterUpdate->getDevice()->getDeviceID(), $deviceId);
        self::assertEquals($sensorAfterUpdate->getSensorName(), $newSensorName);
    }

    public function test_full_successful_response(): void
    {
        $sensors = $this->sensorRepository->findAll();

        /** @var Sensor $sensorToUpdate */
        $sensorToUpdate = $sensors[0];

        $newSensorName = 'newName';

        $this->client->request(
            Request::METHOD_PATCH,
            sprintf(self::UPDATE_SENSOR_URL, $sensorToUpdate->getSensorID()),
            [RequestQueryParameterHandler::RESPONSE_TYPE => RequestTypeEnum::FULL->value],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
            json_encode(['sensorName' => $newSensorName]),
        );

        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
        self::assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);

        $payload = $responseData['payload'];

        self::assertEquals($sensorToUpdate->getSensorID(), $payload['sensorID']);
        self::assertEquals($newSensorName, $payload['sensorName']);

        self::assertEquals($sensorToUpdate->getCreatedBy()->getEmail(), $payload['createdBy']['email']);
        self::assertEquals($sensorToUpdate->getCreatedBy()->getFirstName(), $payload['createdBy']['firstName']);
        self::assertEquals($sensorToUpdate->getCreatedBy()->getLastName(), $payload['createdBy']['lastName']);
        self::assertEquals($sensorToUpdate->getCreatedBy()->getProfilePic(), $payload['createdBy']['profilePicture']);
        self::assertArrayNotHasKey('roles', $payload['createdBy']);
        self::assertEquals($sensorToUpdate->getCreatedBy()->getGroup()->getGroupID(), $payload['createdBy']['group']['groupID']);
        self::assertEquals($sensorToUpdate->getCreatedBy()->getGroup()->getGroupName(), $payload['createdBy']['group']['groupName']);

        self::assertEquals($sensorToUpdate->getDevice()->getDeviceID(), $payload['device']['deviceID']);
        self::assertEquals($sensorToUpdate->getDevice()->getDeviceName(), $payload['device']['deviceName']);
        self::assertEquals($sensorToUpdate->getDevice()->getGroupObject()->getGroupID(), $payload['device']['group']['groupID']);
        self::assertEquals($sensorToUpdate->getDevice()->getRoomObject()->getRoomID(), $payload['device']['room']['roomID']);
        self::assertArrayNotHasKey('secret', $payload['device']);

        self::assertEquals($sensorToUpdate->getSensorTypeObject()->getSensorType(), $payload['sensorType']['sensorTypeName']);
        self::assertEquals($sensorToUpdate->getSensorTypeObject()->getSensorTypeID(), $payload['sensorType']['sensorTypeID']);
        self::assertEquals($sensorToUpdate->getSensorTypeObject()->getDescription(), $payload['sensorType']['sensorTypeDescription']);
    }

    public function test_adding_sensor_to_device_with_same_name(): void
    {
        $sensors = $this->sensorRepository->findAll();
        /** @var Sensor $sensor */
        $sensor = array_pop($sensors);

        foreach ($sensors as $sens) {
            if ($sens->getDevice()->getDeviceID() === $sensor->getDevice()->getDeviceID()) {
                $sensorToUpdate = $sens;
                break;
            }
        }

        if (!isset($sensorToUpdate)) {
            self::fail('No sensor found to update');
        }

        $content = [
            'sensorName' => $sensor->getSensorName(),
            'deviceID' => $sensor->getDevice()->getDeviceID(),
        ];

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_SENSOR_URL, $sensorToUpdate->getSensorID()),
            [RequestQueryParameterHandler::RESPONSE_TYPE => RequestTypeEnum::FULL->value],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
            json_encode($content),
        );

        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $title = $responseData['title'];
        $errorsPayload = $responseData['errors'];

        self::assertEquals(UpdateSensorController::BAD_REQUEST_NO_DATA_RETURNED, $title);
        self::assertEquals([sprintf(DuplicateSensorException::MESSAGE, $sensor->getSensorName())], $errorsPayload);
    }

    /**
     * @dataProvider wrongHttpsMethodDataProvider
     */
    public function test_using_wrong_http_method(string $httpVerb): void
    {
        $sensors = $this->sensorRepository->findAll();
        $sensor = $sensors[0];

        $this->client->request(
            $httpVerb,
            sprintf(self::UPDATE_SENSOR_URL, $sensor->getSensorID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );

        self::assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $this->client->getResponse()->getStatusCode());
    }

    public function wrongHttpsMethodDataProvider(): Generator
    {
        yield [Request::METHOD_GET];
        yield [Request::METHOD_POST];
        yield [Request::METHOD_DELETE];
    }
}
