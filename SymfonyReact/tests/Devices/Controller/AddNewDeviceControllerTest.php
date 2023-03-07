<?php

namespace App\Tests\Devices\Controller;

use App\ORM\DataFixtures\Core\RoomFixtures;
use App\ORM\DataFixtures\Core\UserDataFixtures;
use App\ORM\DataFixtures\ESP8266\ESP8266DeviceFixtures;
use App\Authentication\Controller\SecurityController;
use App\Authentication\Entity\GroupNameMapping;
use App\Common\API\APIErrorMessages;
use App\Common\API\HTTPStatusCodes;
use App\Devices\Entity\Devices;
use App\Tests\Traits\TestLoginTrait;
use App\User\Entity\GroupNames;
use App\User\Entity\Room;
use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AddNewDeviceControllerTest extends WebTestCase
{
    use TestLoginTrait;

    private const ADD_NEW_DEVICE_PATH = '/HomeApp/api/user/user-devices/add';

    private const UNIQUE_NEW_DEVICE_NAME = 'newDeviceName';

    private const NEW_DEVICE_PASSWORD = 'Test1234';

    private ?string $userToken = null;

    private ?EntityManagerInterface $entityManager;

    private KernelBrowser $client;

    private GroupNames $groupName;

    private Room $room;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->groupName = $this->entityManager->getRepository(GroupNames::class)->findOneByName(UserDataFixtures::ADMIN_GROUP_ONE);
        $this->room = $this->entityManager->getRepository(Room::class)->findRoomByName( RoomFixtures::LIVING_ROOM);
        $this->userToken = $this->setUserToken($this->client);
    }

    public function test_sending_wrong_encoding_request(): void
    {
        $formData = [
            'deviceName' => self::UNIQUE_NEW_DEVICE_NAME,
            'devicePassword' => self::NEW_DEVICE_PASSWORD,
            'deviceGroup' => $this->groupName->getGroupNameID(),
            'deviceRoom' => $this->room->getRoomID(),
        ];

        $this->client->request(
            Request::METHOD_POST,
            self::ADD_NEW_DEVICE_PATH,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
            implode(',', $formData)
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        self::assertEquals(APIErrorMessages::FORMAT_NOT_SUPPORTED, $responseData['title']);
    }

    //  Add addNewDevice
    public function test_add_new_device(): void
    {
        $formData = [
            'deviceName' => self::UNIQUE_NEW_DEVICE_NAME,
            'devicePassword' => self::NEW_DEVICE_PASSWORD,
            'deviceGroup' => $this->groupName->getGroupNameID(),
            'deviceRoom' => $this->room->getRoomID(),
        ];

        $jsonData = json_encode($formData);

        $this->client->request(
            Request::METHOD_POST,
            self::ADD_NEW_DEVICE_PATH,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
            $jsonData
        );

        /** @var Devices $device */
        $device = $this->entityManager->getRepository(Devices::class)->findOneBy(['deviceName' => self::UNIQUE_NEW_DEVICE_NAME]);

        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $payload = $responseData['payload'];

        self::assertNotNull($payload['deviceNameID']);
        self::assertNull($payload['ipAddress']);
        self::assertNull($payload['externalIpAddress']);

        self::assertEquals(self::UNIQUE_NEW_DEVICE_NAME, $payload['deviceName']);
        self::assertEquals($this->groupName->getGroupNameID(), $payload['groupNameID']);
        self::assertEquals($this->room->getRoomID(), $payload['roomID']);
        self::assertEquals(UserDataFixtures::ADMIN_USER_EMAIL_ONE, $payload['createdBy']);
        self::assertEquals(Devices::ROLE, $payload['roles'][0]);
        self::assertEquals(self::NEW_DEVICE_PASSWORD, $payload['secret']);

        self::assertInstanceOf(Devices::class, $device);

        self::assertEquals(HTTPStatusCodes::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function test_add_duplicate_device_name_same_room(): void
    {
        $formData = [
            'deviceName' => ESP8266DeviceFixtures::LOGIN_TEST_ACCOUNT_NAME_ADMIN_GROUP_ONE['name'],
            'devicePassword' => self::NEW_DEVICE_PASSWORD,
            'deviceGroup' => $this->groupName->getGroupNameID(),
            'deviceRoom' => $this->room->getRoomID(),
        ];

        $jsonData = json_encode($formData);
        $this->client->request(
            Request::METHOD_POST,
            self::ADD_NEW_DEVICE_PATH,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
            $jsonData
        );

        /** @var Devices $device */
        $device = $this->entityManager->getRepository(Devices::class)->findBy(['deviceName' => $formData['deviceName']]);
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        self::assertStringContainsString(sprintf(
            'Your group already has a device named %s that is in room %s',
            ESP8266DeviceFixtures::LOGIN_TEST_ACCOUNT_NAME_ADMIN_GROUP_ONE['name'],
            $this->room->getRoom(),
        ), $responseData['errors'][0]);

        self::assertCount(1, $device);
        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function test_sending_malformed_request_missing_name(): void
    {
        $formData = [
            'devicePassword' => self::NEW_DEVICE_PASSWORD,
            'deviceGroup' => $this->groupName->getGroupNameID(),
            'deviceRoom' => $this->room->getRoomID(),
        ];

        $jsonData = json_encode($formData);

        $this->client->request(
            Request::METHOD_POST,
            self::ADD_NEW_DEVICE_PATH,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
            $jsonData
        );
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        self::assertStringContainsString('Device name cannot be null', $responseData['errors'][0]);
        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function test_sending_malformed_request_missing_group(): void
    {
        $formData = [
            'deviceName' => self::UNIQUE_NEW_DEVICE_NAME,
            'devicePassword' => self::NEW_DEVICE_PASSWORD,
            'deviceRoom' => $this->room->getRoomID(),
        ];

        $jsonData = json_encode($formData);

        $this->client->request(
            Request::METHOD_POST,
            self::ADD_NEW_DEVICE_PATH,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
            $jsonData,
        );

        /** @var Devices $device */
        $device = $this->entityManager->getRepository(Devices::class)->findOneBy(['deviceName' => $formData['deviceName']]);
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        self::assertNull($device);
        self::assertStringContainsString('Device group cannot be null', $responseData['errors'][0]);
        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function test_sending_malformed_request_missing_room(): void
    {
        $formData = [
            'deviceName' => self::UNIQUE_NEW_DEVICE_NAME,
            'devicePassword' => self::NEW_DEVICE_PASSWORD,
            'deviceGroup' => $this->groupName->getGroupNameID(),
        ];

        $jsonData = json_encode($formData);

        $this->client->request(
            Request::METHOD_POST,
            self::ADD_NEW_DEVICE_PATH,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
            $jsonData,
        );

        /** @var Devices $device */
        $device = $this->entityManager->getRepository(Devices::class)->findOneBy(['deviceName' => $formData['deviceName']]);
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        self::assertNull($device);
        self::assertStringContainsString('Device room cannot be null', $responseData['errors'][0]);
        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function test_sending_malformed_request_missing_password(): void
    {
        $formData = [
            'deviceName' => self::UNIQUE_NEW_DEVICE_NAME,
            'deviceGroup' => $this->groupName->getGroupNameID(),
            'deviceRoom' => $this->room->getRoomID(),
        ];

        $jsonData = json_encode($formData);

        $this->client->request(
            Request::METHOD_POST,
            self::ADD_NEW_DEVICE_PATH,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
            $jsonData,
        );

        /** @var Devices $device */
        $device = $this->entityManager->getRepository(Devices::class)->findOneBy(['deviceName' => $formData['deviceName']]);
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        self::assertNull($device);
        self::assertStringContainsString('Device password cannot be null', $responseData['errors'][0]);
        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }


    /**
     * @dataProvider addingDeviceSendingMalformedRequestDataProvider
     */
    public function test_adding_device_sending_malformed_request(array $formData, array $errors): void
    {
        $jsonData = json_encode($formData);
        $this->client->request(
            Request::METHOD_POST,
            self::ADD_NEW_DEVICE_PATH,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '. $this->userToken],
            $jsonData,
        );
        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        /** @var Devices $device */
        $device = $this->entityManager->getRepository(Devices::class)->findOneBy(['deviceName' => $formData['deviceName']]);

        self::assertNull($device);
        self::assertEquals($errors, $responseData['errors']);
        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function addingDeviceSendingMalformedRequestDataProvider(): Generator
    {
        yield [
            'formData' => [
                'deviceName' => self::UNIQUE_NEW_DEVICE_NAME,
                'devicePassword' => self::NEW_DEVICE_PASSWORD,
                'deviceGroup' => 'string',
                'deviceRoom' => 1,
            ],
            'errorMessage' => [
                'Device group value is "string" and not a valid integer'
            ],
        ];

        yield [
            'formData' => [
                'deviceName' => self::UNIQUE_NEW_DEVICE_NAME,
                'devicePassword' => self::NEW_DEVICE_PASSWORD,
                'deviceGroup' => 2,
                'deviceRoom' => 'string',
            ],
            'errorMessage' => [
                'Device room value is "string" and not a valid integer'
            ],
        ];

        yield [
            'formData' => [
                'deviceName' => ['dfg'],
                'devicePassword' => ['dfg'],
                'deviceGroup' => ['dfg'],
                'deviceRoom' => ['dfg'],
            ],
            'errorMessage' => [
                'Device name value is array and not a valid string',
                'Device password value is array and not a valid string',
                'Device group value is array and not a valid integer',
                'Device room value is array and not a valid integer',
            ],
        ];
    }

    public function test_adding_device_name_too_long(): void
    {
        $formData = [
            'deviceName' => 'thisNameIsWaaaaaaaayTooooLoooongthisNameIsWaaaaaaaayTooooLoooong',
            'devicePassword' => self::NEW_DEVICE_PASSWORD,
            'deviceGroup' => $this->groupName->getGroupNameID(),
            'deviceRoom' => $this->room->getRoomID(),
        ];

        $jsonData = json_encode($formData);

        $this->client->request(
            Request::METHOD_POST,
            self::ADD_NEW_DEVICE_PATH,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
            $jsonData,
        );

        /** @var Devices $device */
        $device = $this->entityManager->getRepository(Devices::class)->findOneBy(['deviceName' => $formData['deviceName']]);
        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertNull($device);
        self::assertStringContainsString('Device name cannot be longer than 50 characters', $responseData['errors'][0]);
        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    /**
     * allowing special characters in device name at the moment @TODO only allow hypens and underscores
     */
    public function test_adding_device_name_special_characters(): void
    {
        $formData = [
            'deviceName' => 'device&&**name',
            'devicePassword' => self::NEW_DEVICE_PASSWORD,
            'deviceGroup' => $this->groupName->getGroupNameID(),
            'deviceRoom' => $this->room->getRoomID(),
        ];

        $jsonData = json_encode($formData);

        $this->client->request(
            Request::METHOD_POST,
            self::ADD_NEW_DEVICE_PATH,
            $formData,
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
            $jsonData,
        );

        /** @var Devices $device */
        $device = $this->entityManager->getRepository(Devices::class)->findOneBy(['deviceName' => $formData['deviceName']]);
        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertNull($device);
        self::assertStringContainsString('The name cannot contain any special characters, please choose a different name', $responseData['errors'][0]);
        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function test_adding_password_name_too_short(): void
    {
        $formData = [
            'deviceName' => 'devicename',
            'devicePassword' => '1',
            'deviceGroup' => $this->groupName->getGroupNameID(),
            'deviceRoom' => $this->room->getRoomID(),
        ];

        $jsonData = json_encode($formData);

        $this->client->request(
            Request::METHOD_POST,
            self::ADD_NEW_DEVICE_PATH,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
            $jsonData,
        );

        /** @var Devices $device */
        $device = $this->entityManager->getRepository(Devices::class)->findOneBy(['deviceName' => $formData['deviceName']]);
        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertNull($device);
        self::assertStringContainsString('Device password must be at least 5 characters long', $responseData['errors'][0]);
        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function test_adding_password_name_too_long(): void
    {
        $formData = [
            'deviceName' => 'devicename',
            'devicePassword' => 'devicePasswordIsWayTooLong1111111111111devicePasswordIsWayTooLong1111111111111devicePasswordIsWayTooLong1111111111111',
            'deviceGroup' => $this->groupName->getGroupNameID(),
            'deviceRoom' => $this->room->getRoomID(),
        ];

        $jsonData = json_encode($formData);

        $this->client->request(
            Request::METHOD_POST,
            self::ADD_NEW_DEVICE_PATH,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
            $jsonData,
        );

        /** @var Devices $device */
        $device = $this->entityManager->getRepository(Devices::class)->findOneBy(['deviceName' => $formData['deviceName']]);
        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertNull($device);
        self::assertStringContainsString('Device password cannot be longer than 100 characters', $responseData['errors'][0]);
        self::assertEquals(HTTPStatusCodes::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function test_adding_device_to_group_not_apart_of(): void
    {
        $userToken = $this->setUserToken($this->client, UserDataFixtures::REGULAR_USER_EMAIL_ONE, UserDataFixtures::REGULAR_PASSWORD);

        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_ONE]);

        $groupNameMappingRepository = $this->entityManager->getRepository(GroupNameMapping::class);

        $groupNameMappingEntities = $groupNameMappingRepository->getAllGroupMappingEntitiesForUser($user);
        $user->setUserGroupMappingEntities($groupNameMappingEntities);

        $groupNameRepository = $this->entityManager->getRepository(GroupNames::class);
        /** @var GroupNames $groupUserIsNotApartOf */
        $groupUserIsNotApartOf = $groupNameRepository->findGroupsUserIsNotApartOf(
            $user,
            $user->getAssociatedGroupNameIds(),
        )[0];

        $formData = [
            'deviceName' => self::UNIQUE_NEW_DEVICE_NAME,
            'devicePassword' => self::NEW_DEVICE_PASSWORD,
            'deviceGroup' => $groupUserIsNotApartOf->getGroupNameID(),
            'deviceRoom' => $this->room->getRoomID(),
        ];

        $jsonData = json_encode($formData);

        $this->client->request(
            Request::METHOD_POST,
            self::ADD_NEW_DEVICE_PATH,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$userToken],
            $jsonData,
        );

        /** @var Devices $device */
        $device = $this->entityManager->getRepository(Devices::class)->findOneBy(['deviceName' => $formData['deviceName']]);
        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertNull($device);
        self::assertStringContainsString(APIErrorMessages::ACCESS_DENIED, $responseData['errors'][0]);
        self::assertEquals(HTTPStatusCodes::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

//    public function test_adding_device_in_room_not_apart_of_admin(): void
//    {
//        /** @var User $user */
//        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::REGULAR_USER]);
//
//        $groupNameMappingRepository = $this->entityManager->getRepository(GroupNameMapping::class);
//
////        $groupNameMappingEntities = $groupNameMappingRepository->getAllGroupMappingEntitiesForUser($user);
////        $user->setUserGroupMappingEntities($groupNameMappingEntities);
//
//        /** @var Room $rooms */
//        $rooms = $this->entityManager->getRepository(Room::class)->findAll();
//
//        foreach ($rooms as $room) {
//            if (($room instanceof Room) && !in_array($room->getGroupNameID()->getGroupNameID(), $user->getGroupNameIds(), true)) {
//                $roomNotApartOf = $room->getRoomID();
//            }
//        }
//
//        if (!isset($roomNotApartOf)) {
//            self::fail('No room found for user that is not apart of');
//        }
//        /** @var GroupNameMapping $groupUserIsNotApartOf */
//        $groupUserIsNotApartOf = $groupNameMappingRepository->findGroupsUserIsNotApartOf($user->getGroupNameIds())[0];
//
//        $formData = [
//            'deviceName' => self::UNIQUE_NEW_DEVICE_NAME,
//            'devicePassword' => self::NEW_DEVICE_PASSWORD,
//            'deviceGroup' => $groupUserIsNotApartOf->getGroupName()->getGroupNameID(),
//            'deviceRoom' => $roomNotApartOf,
//        ];
//
//        $jsonData = json_encode($formData);
//
//        $this->client->request(
//            Request::METHOD_POST,
//            self::ADD_NEW_DEVICE_PATH,
//            [],
//            [],
//            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
//            $jsonData,
//        );
//        /** @var Devices $device */
//        $device = $this->entityManager->getRepository(Devices::class)->findOneBy(['deviceName' => $formData['deviceName']]);
//        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
//
////        dd($responseData);
//        self::assertEquals('Request Successful', $responseData['title']);
//        self::assertArrayHasKey('secret', $responseData['payload']);
//        self::assertArrayHasKey('deviceNameID', $responseData['payload']);
//
//        self::assertEquals(self::UNIQUE_NEW_DEVICE_NAME, $responseData['payload']['deviceName']);
////        dd($this->groupName, $responseData['payload']['groupNameID']);
//        self::assertEquals($groupUserIsNotApartOf->getGroupName()->getGroupNameID(), $responseData['payload']['groupNameID']);
//        self::assertEquals($this->room->getRoomID(), $responseData['payload']['roomID']);
//        self::assertEquals(UserDataFixtures::ADMIN_USER, $responseData['payload']['createdBy']);
//        self::assertEquals(Devices::ROLE, $responseData['payload']['roles'][0]);
//
//        self::assertArrayHasKey('secret', $responseData['payload']);
//        self::assertNotNull($responseData['payload']['secret']);
//
//        self::assertEquals(HTTPStatusCodes::HTTP_OK, $this->client->getResponse()->getStatusCode());
//        self::assertInstanceOf(Devices::class, $device);
//    }

//@TODO fix this and the one above for room group name
//    public function test_adding_device_in_room_not_apart_of_none_admin(): void
//    {
//        $user = $this->entityManager->getRepository(UserDTOs::class)->findOneBy(['email' => UserDataFixtures::REGULAR_USER]);
//
//        $userToken = $this->setUserToken($this->client, UserDataFixtures::SECOND_REGULAR_USER_ADMIN_GROUP, UserDataFixtures::REGULAR_PASSWORD);
//        $groupNameMappingRepository = $this->entityManager->getRepository(GroupNameMapping::class);
//
////        $groupNameMappingEntities = $groupNameMappingRepository->getAllGroupMappingEntitiesForUser($user);
////        $user->setUserGroupMappingEntities($groupNameMappingEntities);
//
//        /** @var Room[] $rooms */
//        $rooms = $this->entityManager->getRepository(Room::class)->findAll();
//
//        foreach ($rooms as $room) {
//            if (($room instanceof Room) && !in_array($room->getGroupNameID()->getGroupNameID(), $user->getGroupNameIds(), true)) {
//                $roomNotApartOf = $room->getRoomID();
//            }
//        }
//
//        if (!isset($roomNotApartOf)) {
//            self::fail('No room found for user that is not apart of');
//        }
//        /** @var GroupNameMapping $groupUserIsNotApartOf */
//        $groupUserIsNotApartOf = $groupNameMappingRepository->findGroupsUserIsNotApartOf($user->getGroupNameIds())[0];
//
//        $formData = [
//            'deviceName' => self::UNIQUE_NEW_DEVICE_NAME,
//            'devicePassword' => self::NEW_DEVICE_PASSWORD,
//            'deviceGroup' => $groupUserIsNotApartOf->getGroupName()->getGroupNameID(),
//            'deviceRoom' => $roomNotApartOf,
//        ];
//
//        $jsonData = json_encode($formData);
//
//        $this->client->request(
//            Request::METHOD_POST,
//            self::ADD_NEW_DEVICE_PATH,
//            [],
//            [],
//            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$userToken],
//            $jsonData,
//        );
//        $device = $this->entityManager->getRepository(Devices::class)->findOneBy(['deviceName' => $formData['deviceName']]);
//        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
//
//        self::assertEquals('You Are Not Authorised To Be Here', $responseData['title']);
//        self::assertArrayHasKey('errors', $responseData);
//        self::assertEquals('You have been denied permission to perform this action', $responseData['errors'][0]);
//        self::assertEquals(HTTPStatusCodes::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
//        self::assertNull($device);
//    }

    public function test_adding_device_unrecognised_room(): void
    {
        $roomRepository = $this->entityManager->getRepository(Room::class);
        while (true) {
            $noneExistentRoomID = random_int(1, 10000);
            $room = $roomRepository->find($noneExistentRoomID);

            if ($room === null) {
                break;
            }
        }

        $formData = [
            'deviceName' => self::UNIQUE_NEW_DEVICE_NAME,
            'devicePassword' => self::NEW_DEVICE_PASSWORD,
            'deviceGroup' => $this->groupName->getGroupNameID(),
            'deviceRoom' => $noneExistentRoomID,
        ];

        $jsonData = json_encode($formData);

        $this->client->request(
            Request::METHOD_POST,
            self::ADD_NEW_DEVICE_PATH,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '. $this->userToken],
            $jsonData,
        );

        /** @var Devices $device */
        $device = $this->entityManager->getRepository(Devices::class)->findOneBy(['deviceName' => $formData['deviceName']]);
        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals('Nothing Found', $responseData['title']);
        self::assertArrayHasKey('errors', $responseData);
        self::assertEquals('Room not found for id ' . $noneExistentRoomID, $responseData['errors'][0]);
        self::assertEquals(HTTPStatusCodes::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
        self::assertNull($device);
    }

    public function test_adding_device_unrecognised_group(): void
    {
        $groupRepository = $this->entityManager->getRepository(GroupNames::class);
        while (true) {
            $noneExistentGroupID = random_int(1, 10000);
            $group = $groupRepository->find($noneExistentGroupID);

            if ($group === null) {
                break;
            }
        }

        $formData = [
            'deviceName' => self::UNIQUE_NEW_DEVICE_NAME,
            'devicePassword' => self::NEW_DEVICE_PASSWORD,
            'deviceGroup' => $noneExistentGroupID,
            'deviceRoom' => $this->room->getRoomID(),
        ];

        $jsonData = json_encode($formData);

        $this->client->request(
            Request::METHOD_POST,
            self::ADD_NEW_DEVICE_PATH,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '. $this->userToken],
            $jsonData,
        );
        $device = $this->entityManager->getRepository(Devices::class)->findOneBy(['deviceName' => $formData['deviceName']]);
        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals('Nothing Found', $responseData['title']);
        self::assertArrayHasKey('errors', $responseData);
        self::assertEquals('Group name not found for id ' . $noneExistentGroupID, $responseData['errors'][0]);
        self::assertEquals(HTTPStatusCodes::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
        self::assertNull($device);
    }

    public function test_cannot_add_device_with_no_token(): void
    {
        $formData = [
            'deviceName' => self::UNIQUE_NEW_DEVICE_NAME,
            'devicePassword' => self::NEW_DEVICE_PASSWORD,
            'deviceGroup' => $this->groupName->getGroupNameID(),
            'deviceRoom' => $this->room->getRoomID(),
        ];

        $jsonData = json_encode($formData);

        $this->client->request(
            Request::METHOD_POST,
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

//    public function test_device_password_is_correct_format(): void
//    {
//        $formData = [
//            'deviceName' => self::UNIQUE_NEW_DEVICE_NAME,
//            'deviceGroup' => $this->groupName->getGroupNameID(),
//            'deviceRoom' => $this->room->getRoomID(),
//        ];
//
//        $jsonData = json_encode($formData);
//
//        $this->client->request(
//            Request::METHOD_POST,
//            self::ADD_NEW_DEVICE_PATH,
//            [],
//            [],
//            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
//            $jsonData,
//        );
//
//        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR)['payload'];
//
//        self::assertMatchesRegularExpression('/^[a-f\d]{32}$/', $responseData['secret']);
//    }

    public function test_new_device_can_login(): void
    {
        $formData = [
            'deviceName' => self::UNIQUE_NEW_DEVICE_NAME,
            'devicePassword' => self::NEW_DEVICE_PASSWORD,
            'deviceGroup' => $this->groupName->getGroupNameID(),
            'deviceRoom' => $this->room->getRoomID(),
        ];

        $jsonData = json_encode($formData);

        $this->client->request(
            Request::METHOD_POST,
            self::ADD_NEW_DEVICE_PATH,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
            $jsonData,
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR)['payload'];

        $createResponseCode = $this->client->getResponse()->getStatusCode();

        $loginFormData = [
            'username' => $responseData['deviceName'],
            'password' => $responseData['secret'],
        ];

        $loginJsonData = json_encode($loginFormData);

        $this->client->request(
            Request::METHOD_POST,
            SecurityController::API_DEVICE_LOGIN,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $loginJsonData,
        );

        $loginResponseData = json_decode($this->client->getResponse()->getContent(), true, 512);

        if (empty($loginResponseData['token'])) {
            self::fail('failed to get token from login after adding new device method: test_new_device_can_login');
        }
        self::assertArrayHasKey('token', $loginResponseData);
        self::assertArrayHasKey('refreshToken', $loginResponseData);
        self::assertEquals(HTTPStatusCodes::HTTP_OK, $this->client->getResponse()->getStatusCode());

        self::assertEquals(self::UNIQUE_NEW_DEVICE_NAME, $responseData['deviceName']);
        self::assertEquals($this->groupName->getGroupNameID(), $responseData['groupNameID']);
        self::assertEquals($this->room->getRoomID(), $responseData['roomID']);
        self::assertEquals(UserDataFixtures::ADMIN_USER_EMAIL_ONE, $responseData['createdBy']);
        self::assertEquals(Devices::ROLE, $responseData['roles'][0]);
        self::assertEquals(HTTPStatusCodes::HTTP_OK, $createResponseCode);

        self::assertArrayHasKey('secret', $responseData);
        self::assertNotNull($responseData['secret']);
    }

//    /**
//     * @dataProvider wrongHttpsMethodDataProvider
//     */
//    public function test_adding_device_wrong_http_method(string $httpVerb): void
//    {
//        $this->client->request(
//            $httpVerb,
//            self::ADD_NEW_DEVICE_PATH,
//            [],
//            [],
//            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
//        );
//
//        self::assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $this->client->getResponse()->getStatusCode());
//    }
//
//    public function wrongHttpsMethodDataProvider(): array
//    {
//        return [
//            [Request::METHOD_GET],
//            [Request::METHOD_PUT],
//            [Request::METHOD_PATCH],
//            [Request::METHOD_DELETE],
//        ];
//    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;

        parent::tearDown();
    }
}
