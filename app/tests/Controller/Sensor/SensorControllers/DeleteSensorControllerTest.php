<?php

namespace App\Tests\Controller\Sensor\SensorControllers;

use App\Controller\Sensor\SensorControllers\DeleteSensorController;
use App\DataFixtures\Core\UserDataFixtures;
use App\Entity\Device\Devices;
use App\Entity\Sensor\Sensor;
use App\Entity\User\Group;
use App\Entity\User\User;
use App\Repository\Device\ORM\DeviceRepository;
use App\Repository\Sensor\Sensors\ORM\SensorRepository;
use App\Repository\User\ORM\GroupRepository;
use App\Repository\User\ORM\UserRepository;
use App\Services\API\APIErrorMessages;
use App\Services\API\CommonURL;
use App\Services\Request\RequestQueryParameterHandler;
use App\Services\Request\RequestTypeEnum;
use App\Tests\Traits\TestLoginTrait;
use Doctrine\ORM\EntityManagerInterface;
use JsonException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DeleteSensorControllerTest extends WebTestCase
{
    private const DELETE_SENSOR_URL = CommonURL::USER_HOMEAPP_API_URL . 'sensor/%d';

    use TestLoginTrait;

    private ?EntityManagerInterface $entityManager;

    private KernelBrowser $client;

    private ?string $userToken = null;

    private SensorRepository $sensorRepository;

    private UserRepository $userRepository;

    private GroupRepository $groupNameRepository;

    private DeviceRepository $deviceRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        try {
            $this->sensorRepository = $this->entityManager->getRepository(Sensor::class);
            $this->userRepository = $this->entityManager->getRepository(User::class);
            $this->groupNameRepository = $this->entityManager->getRepository(Group::class);
            $this->deviceRepository = $this->entityManager->getRepository(Devices::class);
            $this->userToken = $this->setUserToken($this->client);
        } catch (JsonException $e) {
            error_log($e);
        }
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
        parent::tearDown();
    }

    public function test_admin_user_can_delete_any_sensor(): void
    {
        /** @var Sensor[] $allSensors */
        $allSensors = $this->sensorRepository->findAll();
        $sensor = $allSensors[array_rand($allSensors)];

        $this->client->request(
            Request::METHOD_DELETE,
            sprintf(self::DELETE_SENSOR_URL, $sensor->getSensorID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $deletedSensor = $this->sensorRepository->findOneBy(['sensorID' => $sensor->getSensorID()]);
        self::assertNull($deletedSensor);
    }

    public function test_regular_user_can_delete_sensors_part_of_same_device_group_name(): void
    {
        $user = $this->userRepository->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_ONE]);
        $userToken = $this->setUserToken($this->client, UserDataFixtures::REGULAR_USER_EMAIL_ONE, UserDataFixtures::REGULAR_PASSWORD);

        $groupsUserIsPartOf = $this->groupNameRepository->findGroupsUserIsApartOf($user, $user->getAssociatedGroupIDs());

        $devicesInGroupsUserIsPartOf = $this->deviceRepository->findBy(['groupID' => $groupsUserIsPartOf]);

        /** @var \App\Entity\Sensor\Sensor[] $sensors */
        $sensors = $this->sensorRepository->findBy([
            'deviceID' => $devicesInGroupsUserIsPartOf,
        ]);

        $sensor = $sensors[array_rand($sensors)];

        $this->client->request(
            Request::METHOD_DELETE,
            sprintf(self::DELETE_SENSOR_URL, $sensor->getSensorID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $userToken],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $deletedSensor = $this->sensorRepository->findOneBy(['sensorID' => $sensor->getSensorID()]);
        self::assertNull($deletedSensor);
    }

    public function test_admin_delete_sensor_full_response(): void
    {
        /** @var Sensor[] $allSensors */
        $allSensors = $this->sensorRepository->findAll();
        $sensor = $allSensors[array_rand($allSensors)];

        $this->client->request(
            Request::METHOD_DELETE,
            sprintf(self::DELETE_SENSOR_URL, $sensor->getSensorID()),
            [RequestQueryParameterHandler::RESPONSE_TYPE => RequestTypeEnum::FULL->value],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );
        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $deletedSensor = $this->sensorRepository->findOneBy(['sensorID' => $sensor->getSensorID()]);
        self::assertNull($deletedSensor);

        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $title = $responseData['title'];
        self::assertEquals(DeleteSensorController::DELETE_SENSOR_SUCCESS_MESSAGE, $title);

        $payload = $responseData['payload'];

        $createdBy = $sensor->getCreatedBy();
        $createdByResponse = $payload['createdBy'];
        self::assertEquals($createdBy->getUserID(), $createdByResponse['userID']);
        self::assertEquals($createdBy->getEmail(), $createdByResponse['email']);
        self::assertEquals($createdBy->getFirstName(), $createdByResponse['firstName']);
        self::assertEquals($createdBy->getLastName(), $createdByResponse['lastName']);
        self::assertArrayNotHasKey('password', $createdByResponse);
        self::assertArrayNotHasKey('roles', $createdByResponse);

        self::assertEquals($createdBy->getGroup()->getGroupID(), $createdByResponse['group']['groupID']);
        self::assertEquals($createdBy->getGroup()->getGroupName(), $createdByResponse['group']['groupName']);

        self::assertEquals($sensor->getSensorID(), $payload['sensorID']);
        self::assertEquals($sensor->getSensorName(), $payload['sensorName']);

        $deletedSensorDevice = $sensor->getDevice();
        $deletedSensorDeviceResponse = $payload['device'];

        self::assertEquals($deletedSensorDevice->getDeviceID(), $deletedSensorDeviceResponse['deviceID']);
        self::assertEquals($deletedSensorDevice->getDeviceName(), $deletedSensorDeviceResponse['deviceName']);

        self::assertEquals($deletedSensorDevice->getGroupObject()->getGroupID(), $deletedSensorDeviceResponse['group']['groupID']);
        self::assertEquals($deletedSensorDevice->getGroupObject()->getGroupName(), $deletedSensorDeviceResponse['group']['groupName']);

        $deletedDeviceRoom = $deletedSensorDevice->getRoomObject();
        $deletedDeviceRoomResponse = $deletedSensorDeviceResponse['room'];

        self::assertEquals($deletedDeviceRoom->getRoomID(), $deletedDeviceRoomResponse['roomID']);
        self::assertEquals($deletedDeviceRoom->getRoom(), $deletedDeviceRoomResponse['roomName']);

        $deletedDeviceSensorType = $sensor->getSensorTypeObject();
        $deletedDeviceSensorTypeResponse = $payload['sensorType'];

        self::assertEquals($deletedDeviceSensorType->getSensorTypeID(), $deletedDeviceSensorTypeResponse['sensorTypeID']);
        self::assertEquals($deletedDeviceSensorType::getReadingTypeName(), $deletedDeviceSensorTypeResponse['sensorTypeName']);
        self::assertEquals($deletedDeviceSensorType->getDescription(), $deletedDeviceSensorTypeResponse['sensorTypeDescription']);

        self::assertArrayHasKey('sensorReadingTypes', $payload);
    }

    public function test_admin_delete_sensor_part_response(): void
    {
        /** @var Sensor[] $allSensors */
        $allSensors = $this->sensorRepository->findAll();
        $sensor = $allSensors[array_rand($allSensors)];

        $this->client->request(
            Request::METHOD_DELETE,
            sprintf(self::DELETE_SENSOR_URL, $sensor->getSensorID()),
            [RequestQueryParameterHandler::RESPONSE_TYPE => RequestTypeEnum::ONLY->value],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );
        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $deletedSensor = $this->sensorRepository->findOneBy(['sensorID' => $sensor->getSensorID()]);
        self::assertNull($deletedSensor);

        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $title = $responseData['title'];
        self::assertEquals(DeleteSensorController::DELETE_SENSOR_SUCCESS_MESSAGE, $title);

        $payload = $responseData['payload'];

        self::assertEquals($sensor->getSensorID(), $payload['sensorID']);
        self::assertEquals($sensor->getSensorName(), $payload['sensorName']);
    }


    public function test_regular_user_cannot_delete_sensors_part_of_different_device_group_name(): void
    {
        $user = $this->userRepository->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_ONE]);
        $userToken = $this->setUserToken($this->client, UserDataFixtures::REGULAR_USER_EMAIL_ONE, UserDataFixtures::REGULAR_PASSWORD);

        $groupsUserIsNotPartOf = $this->groupNameRepository->findGroupsUserIsNotApartOf($user);

        $devicesInGroupsUserIsNotPartOf = $this->deviceRepository->findBy(['groupID' => $groupsUserIsNotPartOf]);

        /** @var Sensor[] $sensors */
        $sensors = $this->sensorRepository->findBy([
            'deviceID' => $devicesInGroupsUserIsNotPartOf,
        ]);

        $sensor = $sensors[array_rand($sensors)];
        $this->client->request(
            Request::METHOD_DELETE,
            sprintf(self::DELETE_SENSOR_URL, $sensor->getSensorID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $userToken],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $title = $responseData['title'];
        $errors = $responseData['errors'];

        self::assertEquals(DeleteSensorController::NOT_AUTHORIZED_TO_BE_HERE, $title);
        self::assertEquals(APIErrorMessages::ACCESS_DENIED, $errors[0]);
    }

//    /**
//     * @dataProvider wrongHttpsMethodDataProvider
//     */
//    public function test_using_wrong_http_method(string $httpVerb): void
//    {
//        $this->client->request(
//            $httpVerb,
//            sprintf(self::DELETE_SENSOR_URL, 1),
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
            [Request::METHOD_POST],
            [Request::METHOD_PUT],
            [Request::METHOD_PATCH],
        ];
    }
}
