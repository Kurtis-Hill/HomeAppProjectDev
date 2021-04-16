<?php


namespace App\Tests\ESPDeviceTests;


use App\API\HTTPStatusCodes;
use App\DataFixtures\Core\UserDataFixtures;

use App\DataFixtures\ESP8266\ESP8266DeviceFixtures;
use App\Entity\Core\GroupNames;
use App\Entity\Core\GroupnNameMapping;
use App\Entity\Core\Room;
use App\Entity\Core\User;
use App\Entity\Devices\Devices;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;


class DeviceServiceUserTest extends WebTestCase
{
    private const API_USER_LOGIN = '/HomeApp/api/login_check';

    private const API_DEVICE_LOGIN = '/HomeApp/device/login_check';

    private const ADD_NEW_DEVICE_PATH = '/HomeApp/api/devices/add-new-device';

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
        $this->room = $this->entityManager->getRepository(Room::class)->findRoomByGroupNameAndName($this->groupName, ESP8266DeviceFixtures::ADMIN_ROOM_NAME);
        try {
            $this->setUserToken();
        } catch (\JsonException $e) {
            error_log($e);
        }
    }

    public function test_login_token_not_null()
    {
        self::assertNotNull($this->userToken);
    }

    public function test_refresh_token_not_null()
    {
        self::assertNotNull($this->userRefreshToken);
    }


    public function test_add_new_device()
    {
        $formData = [
            'device-name' => self::UNIQUE_NEW_DEVICE_NAME,
            'device-group' => $this->groupName->getGroupNameID(),
            'device-room' => $this->room->getRoomID(),
        ];

        $this->client->request(
            'POST',
            self::ADD_NEW_DEVICE_PATH,
            $formData,
            [],
            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
        );

        self::assertEquals(HTTPStatusCodes::HTTP_CREATED, $this->client->getResponse()->getStatusCode());
    }


    public function test_add_duplicate_device_name_same_room()
    {
        $formData = [
            'device-name' => ESP8266DeviceFixtures::LOGIN_TEST_ACCOUNT_NAME['name'],
            'device-group' => $this->groupName->getGroupNameID(),
            'device-room' => $this->room->getRoomID(),
        ];

        $this->client->request(
            'POST',
            self::ADD_NEW_DEVICE_PATH,
            $formData,
            [],
            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
        );

        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }


    public function test_sending_malformed_request_missing_name()
    {
        $formData = [
            'device-group' => $this->groupName->getGroupNameID(),
            'device-room' => $this->room->getRoomID(),
        ];

        $this->client->request(
            'POST',
            self::ADD_NEW_DEVICE_PATH,
            $formData,
            [],
            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );

        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function test_sending_malformed_request_missing_group()
    {
        $formData = [
            'device-name' => self::UNIQUE_NEW_DEVICE_NAME,
            'device-room' => $this->room->getRoomID(),
        ];

        $this->client->request(
            'POST',
            self::ADD_NEW_DEVICE_PATH,
            $formData,
            [],
            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
        );

        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function test_sending_malformed_request_missing_room()
    {
        $formData = [
            'device-name' => self::UNIQUE_NEW_DEVICE_NAME,
            'device-group' => $this->groupName->getGroupNameID(),
        ];

        $this->client->request(
            'POST',
            self::ADD_NEW_DEVICE_PATH,
            $formData,
            [],
            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );

        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function test_sending_malfomed_group_id_string()
    {
        $formData = [
            'device-name' => self::UNIQUE_NEW_DEVICE_NAME,
            'device-group' => 'string',
            'device-room' => $this->room->getRoomID(),
        ];

        $this->client->request(
            'POST',
            self::ADD_NEW_DEVICE_PATH,
            $formData,
            [],
            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
        );

        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function test_adding_device_name_too_long()
    {
        $formData = [
            'device-name' => 'thisNameIsWaaaaaaaayTooooLoooong',
            'device-group' => $this->groupName->getGroupNameID(),
            'device-room' => $this->room->getRoomID(),
        ];

        $this->client->request(
            'POST',
            self::ADD_NEW_DEVICE_PATH,
            $formData,
            [],
            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
        );

        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function test_adding_device_name_special_characters()
    {
        $formData = [
            'device-name' => 'device&&**name',
            'device-group' => $this->groupName->getGroupNameID(),
            'device-room' => $this->room->getRoomID(),
        ];

        $this->client->request(
            'POST',
            self::ADD_NEW_DEVICE_PATH,
            $formData,
            [],
            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
        );

        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function test_adding_device_to_group_not_apart_of()
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::ADMIN_USER]);

        $groupNameMappingRepository = $this->entityManager->getRepository(GroupnNameMapping::class);

        $groupNameMappingEntities = $groupNameMappingRepository->getAllGroupMappingEntitiesForUser($user);
        $user->setUserGroupMappingEntities($groupNameMappingEntities);

        $groupUserIsNotApartOf = $groupNameMappingRepository->findGroupsUserIsNotApartOf($groupNameMappingEntities)[0];

        $formData = [
            'device-name' => self::UNIQUE_NEW_DEVICE_NAME,
            'device-group' => $groupUserIsNotApartOf->getGroupNameID()->getGroupNameID(),
            'device-room' => $this->room->getRoomID(),
        ];

        $this->client->request(
            'POST',
            self::ADD_NEW_DEVICE_PATH,
            $formData,
            [],
            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
        );

        self::assertStringContainsString(GroupNames::NOT_PART_OF_THIS_GROUP_ERROR_MESSAGE, json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR)['responseData'][0]);
    }


    public function test_cannot_add_device_with_no_token()
    {
        $formData = [
            'device-name' => self::UNIQUE_NEW_DEVICE_NAME,
            'device-group' => $this->groupName->getGroupNameID(),
            'device-room' => $this->room->getRoomID(),
        ];

        $this->client->request(
            'POST',
            self::ADD_NEW_DEVICE_PATH,
            $formData,
            [],
            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded', 'HTTP_AUTHORIZATION' => 'BEARER']
        );

        self::assertEquals(HTTPStatusCodes::HTTP_UNAUTHORISED, $this->client->getResponse()->getStatusCode());
    }


    public function test_device_password_is_sent_back_with_response()
    {
        $formData = [
            'device-name' => self::UNIQUE_NEW_DEVICE_NAME,
            'device-group' => $this->groupName->getGroupNameID(),
            'device-room' => $this->room->getRoomID(),
        ];

        $this->client->request(
            'POST',
            self::ADD_NEW_DEVICE_PATH,
            $formData,
            [],
            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR)['responseData'];

        self::assertArrayHasKey('secret', $responseData);
    }

    public function test_device_password_is_sent_back_with_response_and_not_null()
    {
        $formData = [
            'device-name' => self::UNIQUE_NEW_DEVICE_NAME,
            'device-group' => $this->groupName->getGroupNameID(),
            'device-room' => $this->room->getRoomID(),
        ];

        $this->client->request(
            'POST',
            self::ADD_NEW_DEVICE_PATH,
            $formData,
            [],
            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR)['responseData'];

        self::assertNotNull($responseData['secret']);
    }

    public function test_device_password_is_correct_format()
    {
        $formData = [
            'device-name' => self::UNIQUE_NEW_DEVICE_NAME,
            'device-group' => $this->groupName->getGroupNameID(),
            'device-room' => $this->room->getRoomID(),
        ];

        $this->client->request(
            'POST',
            self::ADD_NEW_DEVICE_PATH,
            $formData,
            [],
            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR)['responseData'];

        self::assertMatchesRegularExpression('/^[a-f0-9]{32}$/', $responseData['secret']);
    }

    public function test_device_id_is_sent_back_with_response()
    {
        $formData = [
            'device-name' => self::UNIQUE_NEW_DEVICE_NAME,
            'device-group' => $this->groupName->getGroupNameID(),
            'device-room' => $this->room->getRoomID(),
        ];

        $this->client->request(
            'POST',
            self::ADD_NEW_DEVICE_PATH,
            $formData,
            [],
            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR)['responseData'];

        self::assertArrayHasKey('deviceID', $responseData);
    }

    public function test_device_id_is_sent_back_with_response_and_not_null()
    {
        $formData = [
            'device-name' => self::UNIQUE_NEW_DEVICE_NAME,
            'device-group' => $this->groupName->getGroupNameID(),
            'device-room' => $this->room->getRoomID(),
        ];

        $this->client->request(
            'POST',
            self::ADD_NEW_DEVICE_PATH,
            $formData,
            [],
            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR)['responseData'];

        self::assertNotNull($responseData['deviceID']);
    }



    public function test_device_is_created()
    {
        $formData = [
            'device-name' => self::UNIQUE_NEW_DEVICE_NAME,
            'device-group' => $this->groupName->getGroupNameID(),
            'device-room' => $this->room->getRoomID(),
        ];

        $this->client->request(
            'POST',
            self::ADD_NEW_DEVICE_PATH,
            $formData,
            [],
            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR)['responseData'];

        $deviceId = $responseData['deviceID'];

        $newDevice = $this->entityManager->getRepository(Devices::class)->findOneBy(['deviceNameID' => $deviceId]);

        self::assertInstanceOf(Devices::class, $newDevice);
    }


    public function test_new_device_can_login()
    {
        $formData = [
            'device-name' => self::UNIQUE_NEW_DEVICE_NAME,
            'device-group' => $this->groupName->getGroupNameID(),
            'device-room' => $this->room->getRoomID(),
        ];

        $this->client->request(
            'POST',
            self::ADD_NEW_DEVICE_PATH,
            $formData,
            [],
            ['CONTENT_TYPE' => 'application/x-www-form-urlencoded', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512)['responseData'];

        $deviceId = $responseData['deviceID'];

        $newDevice = $this->entityManager->getRepository(Devices::class)->findOneBy(['deviceNameID' => $deviceId]);

        $this->client->request(
            'POST',
            self::API_DEVICE_LOGIN,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"username":"'.$newDevice->getDeviceName().'","password":"'.$responseData['secret'].'"}'
        );

        $requestCode = $this->client->getResponse()->getStatusCode();

        self::assertEquals(HTTPStatusCodes::HTTP_OK, $requestCode);
    }


    // @TODO add these when delete device functionality has been added
    public function test_admin_can_delete_device_to_a_regular_owned_room_regular_owned_group()
    {

    }

    public function test_regular_user_can_delete_device_to_admin_owned_room_admin_owned_group()
    {

    }


    /**
     * @return mixed|string|KernelBrowser|null
     * @throws \JsonException
     */
    private function setUserToken()
    {
        if ($this->userToken === null) {
            $this->client->request(
                'POST',
                self::API_USER_LOGIN,
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

}
