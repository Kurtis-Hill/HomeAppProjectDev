<?php

namespace App\Tests\Controller\Sensor\SensorControllers;

use App\Controller\Sensor\SensorControllers\UpdateSensorController;
use App\DataFixtures\Core\UserDataFixtures;
use App\DataFixtures\ESP8266\ESP8266DeviceFixtures;
use App\Entity\Device\Devices;
use App\Entity\Sensor\Sensor;
use App\Entity\User\Group;
use App\Entity\User\User;
use App\Exceptions\Sensor\DuplicateSensorException;
use App\Repository\Device\ORM\DeviceRepositoryInterface;
use App\Repository\Sensor\Sensors\SensorRepositoryInterface;
use App\Repository\User\ORM\GroupRepositoryInterface;
use App\Repository\User\ORM\UserRepositoryInterface;
use App\Services\API\APIErrorMessages;
use App\Services\API\CommonURL;
use App\Services\Request\RequestQueryParameterHandler;
use App\Services\Request\RequestTypeEnum;
use App\Tests\Controller\ControllerTestCase;
use App\Tests\Traits\TestLoginTrait;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateSensorControllerTest extends ControllerTestCase
{
    private const UPDATE_SENSOR_URL = CommonURL::USER_HOMEAPP_API_URL . 'sensor/%d';

    private SensorRepositoryInterface $sensorRepository;

    private DeviceRepositoryInterface $deviceRepository;

    private UserRepositoryInterface $userRepository;

    private GroupRepositoryInterface $groupNameRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->sensorRepository = $this->entityManager->getRepository(Sensor::class);
        $this->deviceRepository = $this->entityManager->getRepository(Devices::class);
        $this->userRepository = $this->entityManager->getRepository(User::class);
        $this->groupNameRepository = $this->entityManager->getRepository(Group::class);
    }

    public function test_sending_wrong_format_should_return_bad_request(): void
    {
        /** @var Sensor[] $sensors */
        $sensors = $this->sensorRepository->findAll();

        $sensor = $sensors[0];

        $content = '?sensorName=Test Sensor&deviceID=Test Device';

        $this->authenticateAdminOne();
        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_SENSOR_URL, $sensor->getSensorID()),
            parameters: [$content],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_UNSUPPORTED_MEDIA_TYPE);
    }


    /**
     * @dataProvider incorrectDataTypesDataProvider
     */
    public function test_sending_incorrect_data_types(
        mixed $sensorName,
        mixed $deviceID,
        mixed $pinNumber,
        mixed $readingInterval,
        array $errorMessage
    ): void {
        /** @var Sensor[] $sensors */
        $sensors = $this->sensorRepository->findAll();

        $sensor = $sensors[0];

        $content = [
            'sensorName' => $sensorName,
            'deviceID' => $deviceID,
            'pinNumber' => $pinNumber,
            'readingInterval' => $readingInterval,
        ];

        $this->authenticateAdminOne();
        $this->client->jsonRequest(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_SENSOR_URL, $sensor->getSensorID()),
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

        self::assertEquals("Validation errors occurred", $title);
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
            'errorMessage' => ['sensorName' => 'This value should be of type string.'],
        ];

        yield [
            'sensorName' => 'sensor name',
            'deviceID' => [123],
            'pinNumber' => 1,
            'readingInterval' => 500,
            'errorMessage' => ['deviceID' => 'This value should be of type int.'],
        ];

        yield [
            'sensorName' => 123,
            'deviceID' => 123,
            'pinNumber' => 1,
            'readingInterval' => 500,
            'errorMessage' => ['sensorName' => 'This value should be of type string.'],
        ];

        yield [
            'sensorName' => ['sensor name'],
            'deviceID' => '123',
            'pinNumber' => 1,
            'readingInterval' => 500,
            'errorMessage' => [
                'deviceID' => 'This value should be of type int.',
                'sensorName' => 'This value should be of type string.',
            ],
        ];

        yield [
            'sensorName' => 'sensorname',
            'deviceID' => 123,
            'pinNumber' => false,
            'readingInterval' => 500,
            'errorMessage' => ['pinNumber' => 'This value should be of type int.'],
        ];

        yield [
            'sensorName' => 'sensorname',
            'deviceID' => 123,
            'pinNumber' => ['1'],
            'readingInterval' => 500,
            'errorMessage' => ['pinNumber' => 'This value should be of type int.'],
        ];

        yield [
            'sensorName' => 'sensorname',
            'deviceID' => 123,
            'pinNumber' => 'string',
            'readingInterval' => 500,
            'errorMessage' => ['pinNumber' => 'This value should be of type int.'],
        ];

        yield [
            'sensorName' => 'sensorname',
            'deviceID' => 123,
            'pinNumber' => 1,
            'readingInterval' => 'string',
            'errorMessage' => ['readingInterval' => 'This value should be of type int.'],
        ];

        yield [
            'sensorName' => 'sensorname',
            'deviceID' => 123,
            'pinNumber' => 1,
            'readingInterval' => ['string'],
            'errorMessage' => ['readingInterval' => 'This value should be of type int.'],
        ];

        yield [
            'sensorName' => 'sensorname',
            'deviceID' => 123,
            'pinNumber' => 1,
            'readingInterval' => false,
            'errorMessage' => ['readingInterval' => 'This value should be of type int.'],
        ];
    }

    public function test_admin_can_change_sensor_to_device_not_apart_of(): void
    {

        /** @var User $user */
        $user = $this->userRepository->findOneBy(['email' => UserDataFixtures::ADMIN_USER_EMAIL_TWO]);


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

        $this->authenticateAdminTwo();
        $this->client->jsonRequest(
            method: Request::METHOD_PUT,
            uri: sprintf(self::UPDATE_SENSOR_URL, $sensorToUpdate->getSensorID()) . '?' . RequestQueryParameterHandler::RESPONSE_TYPE . '=' . RequestTypeEnum::FULL->value,
            parameters: ['deviceID' => $deviceID, 'sensorName' => $newSensorName, 'pinNumber' => 10, 'readingInterval' => 1000],
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

        $sensorAfterUpdate = $this->sensorRepository->find($sensorToUpdate->getSensorID());
        self::assertSensorIsSameAsExpected($sensorAfterUpdate, $payload);
    }

    public function test_user_cannot_change_sensor_to_group_not_apart_of(): void
    {
        /** @var User $user */
        $user = $this->userRepository->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_TWO]);


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

        $this->authenticateRegularUserTwo();
        $this->client->jsonRequest(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_SENSOR_URL, $sensorToUpdate->getSensorID()) . '?' . RequestQueryParameterHandler::RESPONSE_TYPE . '=' . RequestTypeEnum::FULL->value,
            ['deviceID' => $deviceId, 'sensorName' => $newSensorName],
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

        $this->authenticateAdminOne();
        //have to assign pin number to avoid clashing with other fixtures
        $this->client->jsonRequest(
            Request::METHOD_PATCH,
            sprintf(self::UPDATE_SENSOR_URL, $sensorToUpdate->getSensorID()) . '?' . RequestQueryParameterHandler::RESPONSE_TYPE . '=' . RequestTypeEnum::FULL->value,
            ['deviceID' => $deviceID, 'pinNumber' => $pinNumber],
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

        self::assertSensorIsSameAsExpected($sensorAfterUpdate, $payload);
        self::assertEquals($pinNumber, $payload['pinNumber']);
    }

    public function test_just_updating_sensor_name(): void
    {
        $sensors = $this->sensorRepository->findAll();

        /** @var Sensor $sensorToUpdate */
        $sensorToUpdate = $sensors[0];

        $newSensorName = 'newName';

        $this->authenticateAdminOne();
        $this->client->jsonRequest(
            Request::METHOD_PATCH,
            sprintf(self::UPDATE_SENSOR_URL, $sensorToUpdate->getSensorID()) . '?' . RequestQueryParameterHandler::RESPONSE_TYPE . '=' . RequestTypeEnum::FULL->value,
            ['sensorName' => $newSensorName],
        );

        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );
        self::assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);

        $payload = $responseData['payload'];

//        self::assertEquals($sensorToUpdate->getSensorID(), $payload['sensorID']);
        self::assertEquals($newSensorName, $payload['sensorName']);
//        self::assertEquals($sensorToUpdate->getDevice()->getDeviceName(), $payload['device']['deviceName']);
//        self::assertEquals($sensorToUpdate->getSensorTypeObject()::getSensorTypeName(), $payload['sensorType']['sensorTypeName']);
//        self::assertEquals($sensorToUpdate->getCreatedBy()->getEmail(), $payload['createdBy']['email']);
//        self::assertEquals($sensorToUpdate->getPinNumber(), $payload['pinNumber']);
        $sensorToUpdate->setSensorName($newSensorName);
        self::assertSensorIsSameAsExpected($sensorToUpdate, $payload);;
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

        $this->authenticateAdminOne();
        $this->client->jsonRequest(
            Request::METHOD_PATCH,
            sprintf(self::UPDATE_SENSOR_URL, $sensor->getSensorID()) . '?' . RequestQueryParameterHandler::RESPONSE_TYPE . '=' . RequestTypeEnum::FULL->value,
            ['pinNumber' => $randomPin],
        );

        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);

        $payload = $responseData['payload'];

        self::assertEquals($randomPin, $payload['pinNumber']);
        $sensor->setPinNumber($randomPin);
        self::assertSensorIsSameAsExpected($sensor, $payload);
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

        $this->authenticateAdminOne();
        $this->client->jsonRequest(
            Request::METHOD_PATCH,
            sprintf(self::UPDATE_SENSOR_URL, $sensor->getSensorID()) . '?' . RequestQueryParameterHandler::RESPONSE_TYPE . '=' . RequestTypeEnum::FULL->value,
            ['readingInterval' => $newReadingInterval],
        );

        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);

        $payload = $responseData['payload'];

        self::assertEquals($newReadingInterval, $payload['readingInterval']);

        $sensorAfterUpdate = $this->sensorRepository->findOneBy(['sensorID' => $sensor->getSensorID()]);

        self::assertSensorIsSameAsExpected($sensorAfterUpdate, $payload);;
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

        $this->authenticateRegularUserOne();
        $this->client->jsonRequest(
            Request::METHOD_PATCH,
            sprintf(self::UPDATE_SENSOR_URL, $sensorToUpdate->getSensorID()) . '?' . RequestQueryParameterHandler::RESPONSE_TYPE . '=' . RequestTypeEnum::FULL->value,
            ['deviceID' => $deviceId, 'sensorName' => $newSensorName],
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

        /** @var Sensor $sensorAfterUpdate */
        $sensorAfterUpdate = $this->sensorRepository->findOneBy(['sensorID' => $sensorToUpdate->getSensorID()]);
        self::assertSensorIsSameAsExpected($sensorAfterUpdate, $payload);
    }

    public function test_full_successful_response(): void
    {
        $sensors = $this->sensorRepository->findAll();

        /** @var Sensor $sensorToUpdate */
        $sensorToUpdate = $sensors[0];

        $newSensorName = 'newName';

        $this->authenticateAdminOne();
        $this->client->jsonRequest(
            Request::METHOD_PATCH,
            sprintf(self::UPDATE_SENSOR_URL, $sensorToUpdate->getSensorID())  . '?' . RequestQueryParameterHandler::RESPONSE_TYPE . '=' . RequestTypeEnum::FULL->value,
            ['sensorName' => $newSensorName],
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

        $sensorToUpdate->setSensorName($newSensorName);
        self::assertSensorIsSameAsExpected($sensorToUpdate, $payload);;

        self::assertArrayNotHasKey('roles', $payload['createdBy']);
        self::assertArrayNotHasKey('secret', $payload['device']);
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

        $this->authenticateAdminOne();
        $this->client->jsonRequest(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_SENSOR_URL, $sensorToUpdate->getSensorID()) . '?' . RequestQueryParameterHandler::RESPONSE_TYPE . '=' . RequestTypeEnum::FULL->value,
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

        self::assertEquals("Validation errors occurred", $title);
        self::assertEquals([sprintf('A sensor with the name "%s" already exists for the device with ID "%d".', $sensor->getSensorName(), $sensor->getDevice()->getDeviceID())], $errorsPayload);
    }

//    /**
//     * @dataProvider wrongHttpsMethodDataProvider
//     */
//    public function test_using_wrong_http_method(string $httpVerb): void
//    {
//        $sensors = $this->sensorRepository->findAll();
//        $sensor = $sensors[0];
//
//        $this->client->request(
//            $httpVerb,
//            sprintf(self::UPDATE_SENSOR_URL, $sensor->getSensorID()),
//            [],
//            [],
//            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
//        );
//
//        self::assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $this->client->getResponse()->getStatusCode());
//    }

    public function wrongHttpsMethodDataProvider(): Generator
    {
        yield [Request::METHOD_GET];
        yield [Request::METHOD_POST];
        yield [Request::METHOD_DELETE];
    }
}
