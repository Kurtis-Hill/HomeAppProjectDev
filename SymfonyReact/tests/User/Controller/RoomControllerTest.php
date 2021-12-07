<?php

namespace App\Tests\User\Controller;

use App\Controller\Core\SecurityController;
use App\DataFixtures\Core\UserDataFixtures;
use App\Entity\Core\GroupNames;
use App\Entity\Core\User;
use App\User\Entity\Room;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RoomControllerTest extends WebTestCase
{
    private const ADD_NEW_ROOM_URL = '/HomeApp/api/user-rooms/add-user-room';

    private EntityManagerInterface $entityManager;

    private KernelBrowser $client;

    private string $userToken;

    private User $user;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::ADMIN_USER]);
        $this->setUserToken();
    }

    private function setUserToken(): void
    {
        $this->client->request(
            'POST',
            SecurityController::API_USER_LOGIN,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"username":"'.UserDataFixtures::ADMIN_USER.'","password":"'.UserDataFixtures::ADMIN_PASSWORD.'"}'
        );

        $requestResponse = $this->client->getResponse();
        $responseData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $this->userToken = $responseData['token'];
    }

    // AddNewRoomTests
    public function test_add_new_room_none_existent_group_data(): void
    {
        while (true) {
            $randomGroup = random_int(0, 1000);

            $groupName = $this->entityManager->getRepository(GroupNames::class)->findOneBy(['groupNameID' => $randomGroup]);

            if (!$groupName instanceof GroupNames) {
                break;
            }
        }

        $formRequestData = [
            'roomName' => 'Testroom',
            'groupId' => $randomGroup,
        ];

        $jsonData = json_encode($formRequestData);

        $this->client->request(
            'POST',
            self::ADD_NEW_ROOM_URL,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
            $jsonData
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        self::assertEquals('Group name not found for id '.$randomGroup, $responseData['errors'][0]);
        self::assertEquals('Bad Request No Data Returned', $responseData['title']);
        self::assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function test_add_new_room_name_too_long(): void
    {
        $formRequestData = [
            'roomName' => 'TestroomTestroomTestroom',
            'groupId' => $this->user->getGroupNameID()->getGroupNameID(),
        ];

        $jsonData = json_encode($formRequestData);

        $this->client->request(
            'POST',
            self::ADD_NEW_ROOM_URL,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
            $jsonData
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        self::assertEquals('Room name cannot be longer than 20 characters', $responseData['errors'][0]);
        self::assertEquals('Bad Request No Data Returned', $responseData['title']);
        self::assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function test_add_new_room_name_too_short(): void
    {
        $formRequestData = [
            'roomName' => 'T',
            'groupId' => $this->user->getGroupNameID()->getGroupNameID(),
        ];

        $jsonData = json_encode($formRequestData);

        $this->client->request(
            'POST',
            self::ADD_NEW_ROOM_URL,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
            $jsonData
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        self::assertEquals('Room name must be at least 2 characters long', $responseData['errors'][0]);
        self::assertEquals('Bad Request No Data Returned', $responseData['title']);
        self::assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function test_add_new_room_wrong_format(): void
    {
        $this->client->request(
            'POST',
            self::ADD_NEW_ROOM_URL,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
            '$formRequestData'
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        self::assertEquals('Request Format not supported', $responseData['errors'][0]);
        self::assertEquals('Bad Request No Data Returned', $responseData['title']);
        self::assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider addNewRoomMissingDataProvider
     */
    public function test_add_new_room_missing_data(?int $groupNameId, ?string $roomName): void
    {
        $formRequestData = [
            'roomName' => $roomName,
            'groupId' => $groupNameId,
        ];

        $jsonData = json_encode($formRequestData);

        $this->client->request(
            'POST',
            self::ADD_NEW_ROOM_URL,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
            $jsonData
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        self::assertEquals('Missing request data', $responseData['errors'][0]);
        self::assertEquals('Bad Request No Data Returned', $responseData['title']);
        self::assertEquals(400, $this->client->getResponse()->getStatusCode());
    }

    public function addNewRoomMissingDataProvider(): Generator
    {
        yield [
            'groupId' => 1,
            'roomName' => null,
        ];
        yield [
            'groupId' => null,
            'roomName' => 'Testroom',
        ];
    }

    /**
     * @param string $userName
     * @dataProvider addNewRoomNotApartOfDataProvider
     */
    public function test_add_new_room_not_apart_of_group(string $userName): void
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $userName]);

        $formRequestData = [
            'roomName' => 'Testroom',
            'groupId' => $user->getGroupNameID()->getGroupNameID(),
        ];

        $jsonData = json_encode($formRequestData);

        $this->client->request(
            'POST',
            self::ADD_NEW_ROOM_URL,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
            $jsonData
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $newRoom = $this->entityManager->getRepository(Room::class)->findOneBy(['room' => 'Testroom']);

        self::assertNull($newRoom);
        self::assertEquals('You have been denied permission to perform this action', $responseData['errors'][0]);
        self::assertEquals('You Are Not Authorised To Be Here', $responseData['title']);
        self::assertEquals(403, $this->client->getResponse()->getStatusCode());
    }

    public function addNewRoomNotApartOfDataProvider(): Generator
    {
        yield [
            'adminUserName' => UserDataFixtures::SECOND_ADMIN_USER_ISOLATED
        ];
        yield [
            'regularUserName' => UserDataFixtures::SECOND_REGULAR_USER_ISOLATED
        ];
    }

    public function test_add_new_room_correct_data(): void
    {
        $formRequestData = [
            'roomName' => 'Testroom',
            'groupId' => $this->user->getGroupNameID()->getGroupNameID(),
        ];

        $jsonData = json_encode($formRequestData);

        $this->client->request(
            'POST',
            self::ADD_NEW_ROOM_URL,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
            $jsonData
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $newRoom = $this->entityManager->getRepository(Room::class)->findOneBy(['room' => 'Testroom']);

        self::assertInstanceOf(Room::class, $newRoom);
        self::assertEquals('Room created successfully', $responseData['payload'][0]);
        self::assertEquals('Request Accepted Successfully Created', $responseData['title']);
        self::assertEquals(201, $this->client->getResponse()->getStatusCode());
    }
    // End Of AddNewRoomTests
}
