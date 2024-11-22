<?php

namespace App\Tests\Controller\User\RoomControllers;

use App\Controller\User\RoomControllers\UpdateRoomController;
use App\DataFixtures\Core\UserDataFixtures;
use App\Entity\User\Room;
use App\Entity\User\User;
use App\Repository\User\ORM\RoomRepository;
use App\Tests\Traits\TestLoginTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateRoomControllerTest extends WebTestCase
{
    use TestLoginTrait;

    private const ROOM_UPDATE_URL = '/HomeApp/api/user/user-rooms/%d';

    private ?EntityManagerInterface $entityManager;

    private KernelBrowser $client;

    private User $user;

    private string $adminToken;

    private string $regularUserToken;

    private RoomRepository $roomRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::ADMIN_USER_EMAIL_ONE]);
        $this->adminToken = $this->setUserToken($this->client);
        $this->regularUserToken = $this->setUserToken($this->client, UserDataFixtures::REGULAR_USER_EMAIL_ONE, UserDataFixtures::REGULAR_PASSWORD);
        $this->roomRepository = $this->entityManager->getRepository(Room::class);
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
        parent::tearDown();
    }

    public function test_admin_can_update_room(): void
    {
        /** @var Room[] $rooms */
        $rooms = $this->roomRepository->findAll();
        $room = $rooms[array_rand($rooms)];

        $formRequestData = [
            'roomName' => 'Testroom',
        ];

        $jsonData = json_encode($formRequestData);

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::ROOM_UPDATE_URL, $room->getRoomID()),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->adminToken, 'CONTENT_TYPE' => 'application/json'],
            $jsonData
        );

        self::assertResponseIsSuccessful();
    }

    public function test_regular_user_cannot_update_room(): void
    {
        $rooms = $this->roomRepository->findAll();
        $room = $rooms[array_rand($rooms)];

        $formRequestData = [
            'roomName' => 'Testroom',
        ];

        $jsonData = json_encode($formRequestData);

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::ROOM_UPDATE_URL, $room->getRoomID()),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->regularUserToken, 'CONTENT_TYPE' => 'application/json'],
            $jsonData
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function test_room_update_with_invalid_data(): void
    {
        /** @var Room[] $rooms */
        $rooms = $this->roomRepository->findAll();
        $room = $rooms[array_rand($rooms)];

        $formRequestData = [
            'roomName' => [],
        ];

        $jsonData = json_encode($formRequestData);

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::ROOM_UPDATE_URL, $room->getRoomID()),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->adminToken, 'CONTENT_TYPE' => 'application/json'],
            $jsonData
        );

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function test_success_response(): void
    {
        /** @var Room[] $rooms */
        $rooms = $this->roomRepository->findAll();
        $room = $rooms[array_rand($rooms)];

        $formRequestData = [
            'roomName' => 'Testroom',
        ];

        $jsonData = json_encode($formRequestData);

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::ROOM_UPDATE_URL, $room->getRoomID()),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->adminToken, 'CONTENT_TYPE' => 'application/json'],
            $jsonData
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        self::assertEquals(UpdateRoomController::REQUEST_SUCCESSFUL, $responseData['title']);
        self::assertEquals('Testroom', $responseData['payload']['roomName']);
        self::assertIsNumeric($responseData['payload']['roomID']);
        self::assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }
}
