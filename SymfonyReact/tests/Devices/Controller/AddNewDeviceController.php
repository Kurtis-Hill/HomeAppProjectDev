<?php

namespace App\Tests\Devices\Controller;

use App\API\HTTPStatusCodes;
use App\Controller\Core\SecurityController;
use App\DataFixtures\Core\RoomFixtures;
use App\DataFixtures\Core\UserDataFixtures;
use App\DataFixtures\ESP8266\ESP8266DeviceFixtures;
use App\Devices\Entity\Devices;
use App\Entity\Core\GroupNames;
use App\Entity\Core\GroupnNameMapping;
use App\Entity\Core\Room;
use App\Entity\Core\User;
use App\Form\FormMessages;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AddNewDeviceController extends WebTestCase
{
    private const ADD_NEW_DEVICE_PATH = '/HomeApp/api/user-devices/add-new-device';

    private const UNIQUE_NEW_DEVICE_NAME = 'newDeviceName';

    /**
     * @var string|null
     */
    private ?string $userToken = null;

    /**
     * @var string|null
     */
    private ?string $userRefreshToken = null;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var KernelBrowser
     */
    private KernelBrowser $client;

    /**
     * @var GroupNames
     */
    private GroupNames $groupName;

    /**
     * @var Room
     */
    private Room $room;


    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->groupName = $this->entityManager->getRepository(GroupNames::class)->findGroupByName(UserDataFixtures::ADMIN_GROUP);
        $this->room = $this->entityManager->getRepository(Room::class)->findRoomByGroupNameAndName($this->groupName, RoomFixtures::ADMIN_ROOM_NAME);
        try {
            $this->setUserToken();
        } catch (JsonException $e) {
            error_log($e);
        }
    }

    /**
     * @return void
     * @throws JsonException
     */
    private function setUserToken(): void
    {
        if ($this->userToken === null) {
            $this->client->request(
                'POST',
                SecurityController::API_USER_LOGIN,
                [],
                [],
                ['CONTENT_TYPE' => 'application/json'],
                '{"username":"'.UserDataFixtures::ADMIN_USER.'","password":"'.UserDataFixtures::ADMIN_PASSWORD.'"}'
            );

            $requestResponse = $this->client->getResponse();
            $requestData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

            $this->userToken = $requestData['token'];
            $this->userRefreshToken = $requestData['refreshToken'];
        }
    }

    public function test_login_token_not_null(): void
    {
        self::assertNotNull($this->userToken);
    }

    public function test_refresh_token_not_null(): void
    {
        self::assertNotNull($this->userRefreshToken);
    }

    //  Add addNewDevice
    public function test_add_new_device(): void
    {
        $formData = [
            'deviceName' => self::UNIQUE_NEW_DEVICE_NAME,
            'deviceGroup' => $this->groupName->getGroupNameID(),
            'deviceRoom' => $this->room->getRoomID(),
        ];

        $jsonData = json_encode($formData);

        $this->client->request(
            'POST',
            self::ADD_NEW_DEVICE_PATH,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
            $jsonData
        );

        $device = $this->entityManager->getRepository(Devices::class)->findOneBy(['deviceName' => self::UNIQUE_NEW_DEVICE_NAME]);

        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR)['payload'];

        self::assertNotNull($responseData['deviceID']);
        self::assertArrayHasKey('secret', $responseData);
        self::assertInstanceOf(Devices::class, $device);
        self::assertEquals(HTTPStatusCodes::HTTP_CREATED, $this->client->getResponse()->getStatusCode());
    }


    public function test_add_duplicate_device_name_same_room(): void
    {
        $formData = [
            'deviceName' => ESP8266DeviceFixtures::LOGIN_TEST_ACCOUNT_NAME['name'],
            'deviceGroup' => $this->groupName->getGroupNameID(),
            'deviceRoom' => $this->room->getRoomID(),
        ];

        $jsonData = json_encode($formData);
        $this->client->request(
            'POST',
            self::ADD_NEW_DEVICE_PATH,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
            $jsonData
        );

        $device = $this->entityManager->getRepository(Devices::class)->findBy(['deviceName' => $formData['deviceName']]);
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        self::assertStringContainsString(sprintf(
            'Your group already has a device named %s that is in room %s',
            ESP8266DeviceFixtures::LOGIN_TEST_ACCOUNT_NAME['name'],
            $this->room->getRoom(),
        ), $responseData['errors'][0]);

        self::assertCount(1, $device);
        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }


    public function test_sending_malformed_request_missing_name(): void
    {
        $formData = [
            'deviceGroup' => $this->groupName->getGroupNameID(),
            'deviceRoom' => $this->room->getRoomID(),
        ];

        $jsonData = json_encode($formData);

        $this->client->request(
            'POST',
            self::ADD_NEW_DEVICE_PATH,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
            $jsonData
        );
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        self::assertStringContainsString(sprintf(FormMessages::SHOULD_NOT_BE_BLANK, 'Device'), $responseData['errors'][0]);
        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function test_sending_malformed_request_missing_group(): void
    {
        $formData = [
            'deviceName' => self::UNIQUE_NEW_DEVICE_NAME,
            'deviceRoom' => $this->room->getRoomID(),
        ];

        $jsonData = json_encode($formData);

        $this->client->request(
            'POST',
            self::ADD_NEW_DEVICE_PATH,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
            $jsonData,
        );

        $device = $this->entityManager->getRepository(Devices::class)->findOneBy(['deviceName' => $formData['deviceName']]);
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        self::assertStringContainsString(FormMessages::FORM_PRE_PROCESS_FAILURE, $responseData['errors'][0]);
        self::assertNull($device);
        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function test_sending_malformed_request_missing_room(): void
    {
        $formData = [
            'deviceName' => self::UNIQUE_NEW_DEVICE_NAME,
            'deviceGroup' => $this->groupName->getGroupNameID(),
        ];

        $jsonData = json_encode($formData);

        $this->client->request(
            'POST',
            self::ADD_NEW_DEVICE_PATH,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
            $jsonData,
        );

        $device = $this->entityManager->getRepository(Devices::class)->findOneBy(['deviceName' => $formData['deviceName']]);
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        self::assertStringContainsString(FormMessages::FORM_PRE_PROCESS_FAILURE, $responseData['errors'][0]);
        self::assertNull($device);
        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function test_adding_device_sending_malfomed_group_id_string(): void
    {
        $formData = [
            'deviceName' => self::UNIQUE_NEW_DEVICE_NAME,
            'deviceGroup' => 'string',
            'deviceRoom' => $this->room->getRoomID(),
        ];

        $jsonData = json_encode($formData);

        $this->client->request(
            'POST',
            self::ADD_NEW_DEVICE_PATH,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
            $jsonData,
        );
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $device = $this->entityManager->getRepository(Devices::class)->findOneBy(['deviceName' => $formData['deviceName']]);

        self::assertStringContainsString('Cannot find group name to add device too', $responseData['errors'][0]);
        self::assertNull($device);
        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function test_adding_device_name_too_long(): void
    {
        $formData = [
            'deviceName' => 'thisNameIsWaaaaaaaayTooooLoooong',
            'deviceGroup' => $this->groupName->getGroupNameID(),
            'deviceRoom' => $this->room->getRoomID(),
        ];

        $jsonData = json_encode($formData);

        $this->client->request(
            'POST',
            self::ADD_NEW_DEVICE_PATH,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
            $jsonData,
        );

        $device = $this->entityManager->getRepository(Devices::class)->findOneBy(['deviceName' => $formData['deviceName']]);
        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertStringContainsString('Device name too long', $responseData['errors'][0]);
        self::assertNull($device);
        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function test_adding_device_name_special_characters(): void
    {
        $formData = [
            'deviceName' => 'device&&**name',
            'deviceGroup' => $this->groupName->getGroupNameID(),
            'deviceRoom' => $this->room->getRoomID(),
        ];

        $jsonData = json_encode($formData);

        $this->client->request(
            'POST',
            self::ADD_NEW_DEVICE_PATH,
            $formData,
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
            $jsonData,
        );

        $device = $this->entityManager->getRepository(Devices::class)->findOneBy(['deviceName' => $formData['deviceName']]);
        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertStringContainsString('The name cannot contain any special characters, please choose a different name', $responseData['errors'][0]);
        self::assertNull($device);
        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function test_adding_device_to_group_not_apart_of(): void
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::ADMIN_USER]);

        $groupNameMappingRepository = $this->entityManager->getRepository(GroupnNameMapping::class);

        $groupNameMappingEntities = $groupNameMappingRepository->getAllGroupMappingEntitiesForUser($user);
        $user->setUserGroupMappingEntities($groupNameMappingEntities);
        $groupUserIsNotApartOf = $groupNameMappingRepository->findGroupsUserIsNotApartOf($user->getGroupNameIds())[0];

        $formData = [
            'deviceName' => self::UNIQUE_NEW_DEVICE_NAME,
            'deviceGroup' => $groupUserIsNotApartOf->getGroupNameID()->getGroupNameID(),
            'deviceRoom' => $this->room->getRoomID(),
        ];

        $jsonData = json_encode($formData);

        $this->client->request(
            'POST',
            self::ADD_NEW_DEVICE_PATH,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
            $jsonData,
        );

        $device = $this->entityManager->getRepository(Devices::class)->findOneBy(['deviceName' => $formData['deviceName']]);
        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertNull($device);
        self::assertStringContainsString(FormMessages::ACCESS_DENIED, $responseData['errors'][0]);
        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }


    public function test_cannot_add_device_with_no_token(): void
    {
        $formData = [
            'deviceName' => self::UNIQUE_NEW_DEVICE_NAME,
            'deviceGroup' => $this->groupName->getGroupNameID(),
            'deviceRoom' => $this->room->getRoomID(),
        ];

        $jsonData = json_encode($formData);

        $this->client->request(
            'POST',
            self::ADD_NEW_DEVICE_PATH,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER'],
            $jsonData,
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertStringContainsString('JWT Token not found', $responseData['message']);
        self::assertEquals(HTTPStatusCodes::HTTP_UNAUTHORISED, $this->client->getResponse()->getStatusCode());
    }

    public function test_device_password_is_sent_back_with_response_and_not_null(): void
    {
        $formData = [
            'deviceName' => self::UNIQUE_NEW_DEVICE_NAME,
            'deviceGroup' => $this->groupName->getGroupNameID(),
            'deviceRoom' => $this->room->getRoomID(),
        ];

        $jsonData = json_encode($formData);

        $this->client->request(
            'POST',
            self::ADD_NEW_DEVICE_PATH,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
            $jsonData,
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR)['payload'];

        self::assertNotNull($responseData['secret']);
    }

    public function test_device_password_is_correct_format(): void
    {
        $formData = [
            'deviceName' => self::UNIQUE_NEW_DEVICE_NAME,
            'deviceGroup' => $this->groupName->getGroupNameID(),
            'deviceRoom' => $this->room->getRoomID(),
        ];

        $jsonData = json_encode($formData);

        $this->client->request(
            'POST',
            self::ADD_NEW_DEVICE_PATH,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
            $jsonData,
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR)['payload'];

        self::assertMatchesRegularExpression('/^[a-f0-9]{32}$/', $responseData['secret']);
    }

    public function test_new_device_can_login(): void
    {
        $formData = [
            'username' => ESP8266DeviceFixtures::LOGIN_TEST_ACCOUNT_NAME['name'],
            'password' => ESP8266DeviceFixtures::LOGIN_TEST_ACCOUNT_NAME['password'],
        ];

        $jsonData = json_encode($formData);

        $this->client->request(
            'POST',
            SecurityController::API_DEVICE_LOGIN,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $jsonData,
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512);

        $requestCode = $this->client->getResponse()->getStatusCode();

        self::assertArrayHasKey('token', $responseData);
        self::assertArrayHasKey('refreshToken', $responseData);
        self::assertEquals(HTTPStatusCodes::HTTP_OK, $requestCode);
    }
}