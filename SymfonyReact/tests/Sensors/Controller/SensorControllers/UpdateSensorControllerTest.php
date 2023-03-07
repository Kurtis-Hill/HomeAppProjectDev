<?php

namespace App\Tests\Sensors\Controller\SensorControllers;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\Devices\Entity\Devices;
use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use App\ORM\DataFixtures\Core\UserDataFixtures;
use App\Sensors\Controller\SensorControllers\UpdateSensorController;
use App\Sensors\Entity\Sensor;
use App\Sensors\Repository\Sensors\SensorRepositoryInterface;
use App\Tests\Traits\TestLoginTrait;
use App\User\Entity\GroupNames;
use App\User\Entity\User;
use App\User\Repository\ORM\GroupNameRepositoryInterface;
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

    private const UPDATE_SENSOR_URL = CommonURL::USER_HOMEAPP_API_URL . 'sensors/%d/update';

    private KernelBrowser $client;

    private ?string $userToken = null;

    private ?EntityManagerInterface $entityManager;

    private SensorRepositoryInterface $sensorRepository;

    private DeviceRepositoryInterface $deviceRepository;

    private UserRepositoryInterface $userRepository;

    private GroupNameRepositoryInterface $groupNameRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->sensorRepository = $this->entityManager->getRepository(Sensor::class);
        $this->deviceRepository = $this->entityManager->getRepository(Devices::class);
        $this->userRepository = $this->entityManager->getRepository(User::class);
        $this->groupNameRepository = $this->entityManager->getRepository(GroupNames::class);

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

        $title = $responseData['title'];
        $errorsPayload = $responseData['errors'];

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        self::assertEquals(UpdateSensorController::BAD_REQUEST_NO_DATA_RETURNED, $title);
        self::assertEquals([APIErrorMessages::FORMAT_NOT_SUPPORTED], $errorsPayload);
    }


    /**
     * @dataProvider incorrectDataTypesDataProvider
     */
    public function test_sending_incorrect_data_types(mixed $sensorName, mixed $deviceID, array $errorMessage): void
    {
        /** @var Sensor[] $sensors */
        $sensors = $this->sensorRepository->findAll();

        $sensor = $sensors[0];

        $content = [
            'sensorName' => $sensorName,
            'deviceID' => $deviceID,
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

        $title = $responseData['title'];
        $errorsPayload = $responseData['errors'];

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        self::assertEquals(UpdateSensorController::BAD_REQUEST_NO_DATA_RETURNED, $title);
        self::assertEquals($errorMessage, $errorsPayload);
    }

    public function incorrectDataTypesDataProvider(): Generator
    {
        yield [
            'sensorName' => [123],
            'deviceID' => 123,
            'errorMessage' => ['sensor name must be of type string|null you provided array'],
        ];

        yield [
            'sensorName' => 'sensor name',
            'deviceID' => [123],
            'errorMessage' => ['device must be of type int|null you provided array'],
        ];

        yield [
            'sensorName' => 123,
            'deviceID' => 123,
            'errorMessage' => ['sensor name must be of type string|null you provided 123'],
        ];

        yield [
            'sensorName' => ['sensor name'],
            'deviceID' => '123',
            'errorMessage' => [
                'sensor name must be of type string|null you provided array',
                'device must be of type int|null you provided "123"',
            ],
        ];
    }

    public function test_admin_can_change_sensor_to_group_not_apart_of(): void
    {
        /** @var User $user */
        $user = $this->userRepository->findOneBy(['email' => UserDataFixtures::ADMIN_USER_EMAIL_TWO]);

        $userToken = $this->setUserToken($this->client, UserDataFixtures::ADMIN_USER_EMAIL_TWO);

        /** @var GroupNames[] $groupUserIsApartOf */
        $groupUserIsApartOf = $this->groupNameRepository->findGroupsUserIsApartOf($user);

        if (empty($groupUserIsApartOf)) {
            self::fail('UserDTOs is not apart of any group');
        }
        foreach ($groupUserIsApartOf as $group) {
            $device = $this->deviceRepository->findOneBy(['groupNameID' => $group]);
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

        /** @var GroupNames[] $groupsUserIsNotApartOf */
        $groupsUserIsNotApartOf = $this->groupNameRepository->findGroupsUserIsNotApartOf($user);

        foreach ($groupsUserIsNotApartOf as $group) {
            $device = $this->deviceRepository->findOneBy(['groupNameID' => $group]);
            if ($device !== null) {
                break;
            }
        }
        $deviceId = $device->getDeviceID();
        $newSensorName = 'newName';

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_SENSOR_URL, $sensorToUpdate->getSensorID()),
            [],
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

        $payload = $responseData['payload'];
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        self::assertEquals($sensorToUpdate->getSensorID(), $payload['sensorNameID']);
        self::assertEquals($newSensorName, $payload['sensorName']);
        self::assertEquals($device->getDeviceName(), $payload['deviceName']);
        self::assertEquals($sensorToUpdate->getSensorTypeObject()->getSensorType(), $payload['sensorType']);
        self::assertEquals($sensorToUpdate->getCreatedBy()->getEmail(), $payload['createdBy']);
    }

    public function test_user_cannot_change_sensor_to_group_not_apart_of(): void
    {
        /** @var User $user */
        $user = $this->userRepository->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_TWO]);

        $userToken = $this->setUserToken($this->client, UserDataFixtures::REGULAR_USER_EMAIL_TWO, UserDataFixtures::REGULAR_PASSWORD);

        /** @var GroupNames[] $groupUserIsApartOf */
        $groupUserIsApartOf = $this->groupNameRepository->findGroupsUserIsApartOf($user);

        if (empty($groupUserIsApartOf)) {
            self::fail('UserDTOs is not apart of any group');
        }
        foreach ($groupUserIsApartOf as $group) {
            $device = $this->deviceRepository->findOneBy(['groupNameID' => $group]);
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

        /** @var GroupNames[] $groupsUserIsNotApartOf */
        $groupsUserIsNotApartOf = $this->groupNameRepository->findGroupsUserIsNotApartOf($user);

        foreach ($groupsUserIsNotApartOf as $group) {
            $device = $this->deviceRepository->findOneBy(['groupNameID' => $group]);
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
            [],
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

        $title = $responseData['title'];
        $errorsPayload = $responseData['errors'];

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
        self::assertEquals(UpdateSensorController::NOT_AUTHORIZED_TO_BE_HERE, $title);
        self::assertEquals([APIErrorMessages::ACCESS_DENIED], $errorsPayload);

    }

    public function test_just_updating_device_id(): void
    {

    }

    public function test_just_updating_sensor_name(): void
    {

    }

    public function test_updating_sensor_correct_data(): void
    {

    }

    /**
     * @dataProvider updatingSensorPartialDataDataProvider
     */
    public function test_updating_sensor_partial_data(array $formData, array $messages): void
    {
    // method PATCH
    }

    public function updatingSensorPartialDataDataProvider(): Generator
    {
        yield [
            'formData' => [
                'sensorName' => 'newName',
            ],
            'messages' => [
                'sensorName' => null,
            ],
        ];


    }

    public function test_adding_sensor_to_device_with_same_name(): void
    {
        $sensors = $this->sensorRepository->findAll();

        $sensor = $sensors[0];

        $content = [
            'sensorName' => $sensor->getSensorName(),
            'deviceID' => $sensor->getDevice()->getDeviceID(),
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

        $title = $responseData['title'];
        $errorsPayload = $responseData['errors'];

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        self::assertEquals(UpdateSensorController::BAD_REQUEST_NO_DATA_RETURNED, $title);
        self::assertEquals([APIErrorMessages::READING_TYPE_NOT_VALID_FOR_SENSOR], $errorsPayload);
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

    public function wrongHttpsMethodDataProvider(): array
    {
        return [
            [Request::METHOD_GET],
            [Request::METHOD_POST],
            [Request::METHOD_DELETE],
        ];
    }
}
