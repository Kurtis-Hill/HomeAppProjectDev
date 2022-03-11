<?php

namespace App\Tests\Devices\Controller;

use App\API\APIErrorMessages;
use App\Authentication\Controller\SecurityController;
use App\Authentication\Entity\GroupNameMapping;
use App\DataFixtures\Core\UserDataFixtures;
use App\DataFixtures\ESP8266\ESP8266DeviceFixtures;
use App\Devices\Entity\Devices;
use App\User\Entity\GroupNames;
use App\User\Entity\Room;
use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Generator;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateDeviceControllerTest extends WebTestCase
{
    private const UPDATE_DEVICE_URL = '/HomeApp/api/user/user-devices/update-device/%d';

    private ?string $userToken = null;

    private ?EntityManagerInterface $entityManager;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->userToken = $this->setUserToken(UserDataFixtures::ADMIN_USER, UserDataFixtures::ADMIN_PASSWORD);
    }

    /**
     * @dataProvider testSendingWrongTypesToUpdateDataProvider
     */
    public function testSendingWrongTypesToUpdate(
        mixed $deviceName,
        mixed $password,
        mixed $deviceGroup,
        mixed $deviceRoom,
        array $errorMessage,
    ): void {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::ADMIN_USER]);
        $device = $this->entityManager->getRepository(Devices::class)->findBy(['groupNameID' => $user->getGroupNameID()])[0];

        $requestData = [
            'deviceName' => $deviceName,
            'password' => $password,
            'deviceGroup' => $deviceGroup,
            'deviceRoom' => $deviceRoom,
        ];

        $jsonPayload = json_encode($requestData);

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_DEVICE_URL, $device->getDeviceNameID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
            $jsonPayload
        );
        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        self::assertEquals($errorMessage, $responseData['errors']);
        self::assertEquals(APIErrorMessages::VALIDATION_ERRORS, $responseData['title']);
    }

    public function testSendingWrongTypesToUpdateDataProvider(): Generator
    {
        yield [
            'deviceName' => [],
            'password' => 'NewPassword',
            'deviceGroup' => 1,
            'deviceRoom' => 1,
            'errorMessage' => [
                'deviceName must be of type string|null you provided array'
            ],
        ];

        yield [
            'deviceName' => 'newDeviceName',
            'password' => ['NewPassword'],
            'deviceGroup' => 1,
            'deviceRoom' => 1,
            'errorMessage' => [
                'password must be of type string|null you provided array'
            ],
        ];

        yield [
            'deviceName' => 'newDeviceName',
            'password' => 'NewPassword',
            'deviceGroup' => 'deviceGroup',
            'deviceRoom' => 1,
            'errorMessage' => [
                'deviceGroup must be of type integer|null you provided "deviceGroup"'
            ],
        ];

        yield [
            'deviceName' => 'newDeviceName',
            'password' => 'NewPassword',
            'deviceGroup' => 1,
            'deviceRoom' => 'deviceRoom',
            'errorMessage' => [
                'deviceRoom must be of type integer|null you provided "deviceRoom"'
            ],
        ];
    }

    public function testSendingDeviceThatDoesntExist(): void
    {
        $deviceRepository = $this->entityManager->getRepository(Devices::class);
        while (true) {
            $nonExistentDeviceID = random_int(1, 100000);

            $device = $deviceRepository->findOneBy(['deviceNameID' => $nonExistentDeviceID]);
            if (!$device instanceof Devices) {
                break;
            }
        }

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_DEVICE_URL, $nonExistentDeviceID),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );

        self::assertEquals(Response::HTTP_NOT_FOUND, $this->client->getResponse()->getStatusCode());
    }

    public function testSendingNoneExistentRoomID(): void
    {
        $roomRepository = $this->entityManager->getRepository(Room::class);
        while (true) {
            $nonExistentRoomID = random_int(1, 100000);

            $room = $roomRepository->findOneBy(['roomID' => $nonExistentRoomID]);
            if (!$room instanceof Room) {
                break;
            }
        }
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::ADMIN_USER]);
        $room = $this->entityManager->getRepository(Devices::class)->findBy(['groupNameID' => $user->getGroupNameID()])[0];


        $requestData = [
            'deviceName' => 'newDeviceName',
            'password' => 'NewPassword',
            'deviceGroup' => $user->getGroupNameID()->getGroupNameID(),
            'deviceRoom' => $nonExistentRoomID,
        ];

        $jsonPayload = json_encode($requestData);

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_DEVICE_URL, $room->getDeviceNameID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
            $jsonPayload
        );
        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        self::assertEquals(['The id provided for room doesnt match any room we have'], $responseData['errors']);
        self::assertEquals('Room not found', $responseData['title']);
    }

    public function testRegularUserCannotUpdateDeviceNotApartOf(): void
    {
        $userToken = $this->setUserToken(UserDataFixtures::SECOND_REGULAR_USER_ISOLATED, UserDataFixtures::REGULAR_PASSWORD);
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::REGULAR_USER]);

        $groupNameMappingRepository = $this->entityManager->getRepository(GroupNameMapping::class);

        $groupNameMappingEntities = $groupNameMappingRepository->getAllGroupMappingEntitiesForUser($user);
        $user->setUserGroupMappingEntities($groupNameMappingEntities);
        $groupUserIsNotApartOf = $groupNameMappingRepository->findGroupsUserIsNotApartOf($user->getGroupNameIds())[0];

        $device = $this->entityManager->getRepository(Devices::class)->findBy(['groupNameID' => $groupUserIsNotApartOf->getGroupNameID()])[0];

        $requestData = [
            'deviceName' => 'newDeviceName',
            'password' => 'NewPassword',
            'deviceGroup' => $user->getGroupNameID()->getGroupNameID(),
            'deviceRoom' => $device->getRoomObject()->getRoomID(),
        ];

        $jsonPayload = json_encode($requestData);

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_DEVICE_URL, $device->getDeviceNameID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $userToken],
            $jsonPayload
        );

        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertEquals('You Are Not Authorised To Be Here', $responseData['title']);
        self::assertEquals('You have been denied permission to perform this action', $responseData['errors'][0]);
        self::assertEquals(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    public function testAdminCanUpdateDeviceNotApartOf(): void
    {    
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::SECOND_ADMIN_USER_ISOLATED]);

        $groupNameMappingRepository = $this->entityManager->getRepository(GroupNameMapping::class);

        $groupNameMappingEntities = $groupNameMappingRepository->getAllGroupMappingEntitiesForUser($user);
        $user->setUserGroupMappingEntities($groupNameMappingEntities);
        $groupUserIsNotApartOf = $groupNameMappingRepository->findGroupsUserIsNotApartOf($user->getGroupNameIds())[0];

        $device = $this->entityManager->getRepository(Devices::class)->findBy(['groupNameID' => $groupUserIsNotApartOf->getGroupNameID()])[0];

        $newDeviceName = 'newDeviceName';
        $requestData = [
            'deviceName' => $newDeviceName,
            'password' => 'NewPassword',
            'deviceGroup' => $user->getGroupNameID()->getGroupNameID(),
            'deviceRoom' => $device->getRoomObject()->getRoomID(),
        ];

        $jsonPayload = json_encode($requestData);

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_DEVICE_URL, $device->getDeviceNameID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
            $jsonPayload
        );

        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertEquals('Device Successfully Updated', $responseData['title']);
        self::assertEquals($newDeviceName, $responseData['payload']['deviceName']);
        self::assertArrayHasKey('groupName', $responseData['payload']);
        self::assertArrayHasKey('groupNameID', $responseData['payload']);
        self::assertArrayHasKey('roomID', $responseData['payload']);
        self::assertArrayHasKey('roomName', $responseData['payload']);
        self::assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

    }

    /**
     * @dataProvider sendingOutOfRangeDeviceUpdateDataProvider
     */
    public function testSendingOutOfRangeDeviceUpdate(string $deviceName, array $errorMessage): void
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(
            ['email' => UserDataFixtures::SECOND_ADMIN_USER_ISOLATED]
        );

        $device = $this->entityManager->getRepository(Devices::class)->findOneBy(
            ['deviceName' => ESP8266DeviceFixtures::ADMIN_TEST_DEVICE['referenceName']]
        );

        $requestData = [
            'deviceName' => $deviceName,
            'password' => 'NewPassword',
            'deviceGroup' => $user->getGroupNameID()->getGroupNameID(),
            'deviceRoom' => $device->getRoomObject()->getRoomID(),
        ];

        $jsonPayload = json_encode($requestData);

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_DEVICE_URL, $device->getDeviceNameID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
            $jsonPayload
        );

        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertEquals($errorMessage, $responseData['errors']);
        self::assertEquals(APIErrorMessages::VALIDATION_ERRORS, $responseData['title']);
        self::assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());

    }

    public function sendingOutOfRangeDeviceUpdateDataProvider(): Generator
    {
        yield [
            'deviceName' => 'newDeviceNamenewDeviceNamenewDeviceName',
            'errorMessage' => [
                'Device name cannot be longer than 20 characters'
            ],
        ];

        yield [
            'deviceName' => 'x',
            'errorMessage' => [
                'Device name must be at least 2 characters long'
            ],
        ];
    }

    public function testUpdatingDeviceCorrectly(): void
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::ADMIN_USER]);
        $groupNameMappingRepository = $this->entityManager->getRepository(GroupNameMapping::class);

        $groupNameMappingEntities = $groupNameMappingRepository->getAllGroupMappingEntitiesForUser($user);
        $user->setUserGroupMappingEntities($groupNameMappingEntities);
        $groupUserIsApartOf = $groupNameMappingRepository->getAllGroupMappingEntitiesForUser($user)[0];

        $device = $this->entityManager->getRepository(Devices::class)->findBy(['groupNameID' => $groupUserIsApartOf->getGroupNameID()])[0];

        $userRooms = $this->entityManager->getRepository(Room::class)->getAllUserRoomsByGroupId($user->getGroupNameIds());
        foreach ($userRooms as $userRoom) {
            if ($userRoom['roomID'] !== $device->getRoomObject()->getRoomID()) {
                $newRoomID = $userRoom['roomID'];
                break;
            }
        }

        foreach ($user->getGroupNameIds() as $groupNameId) {
            if ($groupNameId !== $device->getGroupNameObject()->getGroupNameID()) {
                $newGroupNameID = $groupNameId;
                break;
            }
        }

        $newDeviceName = 'newDeviceName';
        $requestData = [
            'deviceName' => $newDeviceName,
            'password' => 'NewPassword',
            'deviceGroup' => $newGroupNameID,
            'deviceRoom' => $newRoomID,
        ];

        $jsonPayload = json_encode($requestData);

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_DEVICE_URL, $device->getDeviceNameID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
            $jsonPayload
        );

        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertEquals('Device Successfully Updated', $responseData['title']);
        self::assertEquals($newDeviceName, $responseData['payload']['deviceName']);
        self::assertEquals($newGroupNameID, $responseData['payload']['groupNameID']);
        self::assertEquals($newRoomID, $responseData['payload']['roomID']);
        self::assertArrayHasKey('groupName', $responseData['payload']);
        self::assertArrayHasKey('groupNameID', $responseData['payload']);
        self::assertArrayHasKey('roomID', $responseData['payload']);
        self::assertArrayHasKey('roomName', $responseData['payload']);
        self::assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function testDeviceWithPasswordUpdatedCanLogIn(): void
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(
            ['email' => UserDataFixtures::SECOND_ADMIN_USER_ISOLATED]
        );

        $device = $this->entityManager->getRepository(Devices::class)->findOneBy(
            ['deviceName' => ESP8266DeviceFixtures::ADMIN_TEST_DEVICE['referenceName']]
        );

        $newDeviceName = 'NewDeviceName';
        $newPassword = 'NewDevicePassword';
        $requestData = [
            'deviceName' => $newDeviceName,
            'password' => $newPassword,
            'deviceGroup' => $user->getGroupNameID()->getGroupNameID(),
            'deviceRoom' => $device->getRoomObject()->getRoomID(),
        ];

        $jsonPayload = json_encode($requestData);

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_DEVICE_URL, $device->getDeviceNameID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
            $jsonPayload
        );

        if ($this->client->getResponse()->getStatusCode() === Response::HTTP_OK) {
            $this->client->request(
                Request::METHOD_POST,
                SecurityController::API_DEVICE_LOGIN,
                [],
                [],
                ['CONTENT_TYPE' => 'application/json'],
                '{"username":"' . $newDeviceName . '","password":"' . $newPassword .'","ipAddress":"192.168.1.2"}'
            );

            $responseData = json_decode(
                $this->client->getResponse()->getContent(),
                true,
                512,
                JSON_THROW_ON_ERROR
            );

            self::assertArrayHasKey('token', $responseData);
            self::assertArrayHasKey('refreshToken', $responseData);
            self::assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        } else {
            throw new RuntimeException('Device was not updated correctly');
        }
    }

    /**
     * @dataProvider sendingPatchRequestDataProvider
     */
    public function testSendingPatchRequest(): void
    {

    }

    public function sendingPatchRequestDataProvider(): Generator
    {
        yield [];
    }









    private function setUserToken(string $name, string $password): string
    {
        $this->client->request(
            Request::METHOD_POST,
            SecurityController::API_USER_LOGIN,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"username":"'.$name.'","password":"'.$password.'"}'
        );

        $requestResponse = $this->client->getResponse();
        $requestData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

        return $requestData['token'];
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
        parent::tearDown();
    }
}
