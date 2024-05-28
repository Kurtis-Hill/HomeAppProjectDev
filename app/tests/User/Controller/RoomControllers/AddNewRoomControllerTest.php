<?php

namespace App\Tests\User\Controller\RoomControllers;

use App\Common\API\CommonURL;
use App\ORM\DataFixtures\Core\UserDataFixtures;
use App\Authentication\Controller\SecurityController;
use App\Common\API\APIErrorMessages;
use App\Tests\Traits\TestLoginTrait;
use App\User\Entity\Group;
use App\User\Entity\Room;
use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AddNewRoomControllerTest extends WebTestCase
{
    use TestLoginTrait;

    private const ADD_NEW_ROOM_URL = CommonURL::USER_HOMEAPP_API_URL . 'user-rooms/add';

    private ?EntityManagerInterface $entityManager;

    private KernelBrowser $client;

    private User $user;

    private string $userToken;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::ADMIN_USER_EMAIL_ONE]);
        $this->userToken = $this->setUserToken($this->client);
    }

    public function test_add_new_room_name_too_long(): void
    {
        $formRequestData = [
            'roomName' => 'TestroomTestroomTestroom',
        ];

        $jsonData = json_encode($formRequestData);

        $this->client->request(
            Request::METHOD_POST,
            self::ADD_NEW_ROOM_URL,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
            $jsonData
        );
        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        self::assertEquals('Room name cannot be longer than 20 characters', $responseData['errors'][0]);
        self::assertEquals('Bad Request No Data Returned', $responseData['title']);
    }

    public function test_add_new_room_name_too_short(): void
    {
        $formRequestData = [
            'roomName' => 'T',
            'groupID' => $this->user->getGroup()->getGroupID(),
        ];

        $jsonData = json_encode($formRequestData);

        $this->client->request(
            Request::METHOD_POST,
            self::ADD_NEW_ROOM_URL,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
            $jsonData
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        self::assertEquals('Room name must be at least 2 characters long', $responseData['errors'][0]);
        self::assertEquals('Bad Request No Data Returned', $responseData['title']);
        self::assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function test_add_new_room_wrong_format(): void
    {
        $this->client->request(
            Request::METHOD_POST,
            self::ADD_NEW_ROOM_URL,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
            '$formRequestData'
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        self::assertEquals(APIErrorMessages::FORMAT_NOT_SUPPORTED, $responseData['errors'][0]);
        self::assertEquals('Bad Request No Data Returned', $responseData['title']);
        self::assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider addNewRoomMissingDataProvider
     */
    public function test_add_new_room_missing_data(
        mixed $roomName,
        mixed $errorMessage
    ): void {
        $formRequestData = [
            'roomName' => $roomName,
        ];

        $jsonData = json_encode($formRequestData);

        $this->client->request(
            Request::METHOD_POST,
            self::ADD_NEW_ROOM_URL,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
            $jsonData
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        self::assertEquals('Validation Errors Occurred', $responseData['title']);
        self::assertEquals($errorMessage, $responseData['errors']);
        self::assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
    }

    public function addNewRoomMissingDataProvider(): Generator
    {
        yield [
            'roomName' => null,
            'errorMessage' => ['roomName cannot be null']
        ];


        yield [
            'roomName' => [],
            'errorMessage' => ['roomName must be a string you have provided array']
        ];

        yield [
            'roomName' => [],
            'errorMessage' => [
                'roomName must be a string you have provided array',
            ]
        ];
    }

    public function test_add_new_room_correct_data_admin(): void
    {
        $formRequestData = [
            'roomName' => 'Testroom',
        ];

        $jsonData = json_encode($formRequestData);

        $this->client->request(
            Request::METHOD_POST,
            self::ADD_NEW_ROOM_URL,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
            $jsonData
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        /** @var Room $newRoom */
        $newRoom = $this->entityManager->getRepository(Room::class)->findOneBy(['room' => 'Testroom']);

        self::assertEquals($formRequestData['roomName'], $responseData['payload']['roomName']);
        self::assertEquals($responseData['payload']['roomID'], $newRoom->getRoomID());

        self::assertInstanceOf(Room::class, $newRoom);
        self::assertEquals('Room created successfully', $responseData['title']);
        self::assertEquals('Testroom', $responseData['payload']['roomName']);
        self::assertEquals(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());
    }

    public function test_add_new_room_correct_data_regular_user(): void
    {
        $userToken = $this->setUserToken(
            $this->client,
            UserDataFixtures::REGULAR_USER_EMAIL_ONE,
            UserDataFixtures::REGULAR_PASSWORD
        );

        $formRequestData = [
            'roomName' => 'Testroom',
        ];

        $jsonData = json_encode($formRequestData);

        $this->client->request(
            Request::METHOD_POST,
            self::ADD_NEW_ROOM_URL,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $userToken, 'CONTENT_TYPE' => 'application/json'],
            $jsonData
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
//        Uncomment if regular users can create rooms
//        $responseData = json_decode($this->client->getResponse()->getContent(), true);
//
//        /** @var Room $newRoom */
//        $newRoom = $this->entityManager->getRepository(Room::class)->findOneBy(['room' => 'Testroom']);
//
//        self::assertEquals($formRequestData['roomName'], $responseData['payload']['roomName']);
//        self::assertEquals($responseData['payload']['roomID'], $newRoom->getRoomID());
//
//        self::assertInstanceOf(Room::class, $newRoom);
//        self::assertEquals('Room created successfully', $responseData['title']);
//        self::assertEquals('Testroom', $responseData['payload']['roomName']);
//        self::assertEquals(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());
    }
    // End Of AddNewRoomTests

    /**
     * @dataProvider wrongHttpsMethodDataProvider
     */
    public function test_using_wrong_http_method(string $httpVerb): void
    {
        $this->client->request(
            $httpVerb,
            self::ADD_NEW_ROOM_URL,
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
            [Request::METHOD_PUT],
            [Request::METHOD_PATCH],
            [Request::METHOD_DELETE],
        ];
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
        parent::tearDown();
    }
}
