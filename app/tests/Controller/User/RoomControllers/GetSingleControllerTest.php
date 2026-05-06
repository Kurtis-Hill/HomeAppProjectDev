<?php

namespace App\Tests\Controller\User\RoomControllers;

use App\DataFixtures\Core\RoomFixtures;
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

class GetSingleControllerTest extends WebTestCase
{
    use TestLoginTrait;

    private const GET_SINGLE_ROOM_URL = '/HomeApp/api/user/user-rooms/%d';

    private ?EntityManagerInterface $entityManager;

    private KernelBrowser $client;

    private string $adminUserToken;

    private string $regularUserToken;

    private User $adminUser;

    private User $regularUser;

    private RoomRepository $roomRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->adminUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::ADMIN_USER_EMAIL_ONE]);
        $this->regularUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_ONE]);
        $this->adminUserToken = $this->setUserToken($this->client);
        $this->regularUserToken = $this->setUserToken($this->client, UserDataFixtures::REGULAR_USER_EMAIL_ONE, UserDataFixtures::REGULAR_PASSWORD);
        $this->roomRepository = $this->entityManager->getRepository(Room::class);
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
        parent::tearDown();
    }

    public function test_getting_room_success_response(): void
    {
        /** @var Room $room */
        $rooms = $this->roomRepository->findAll();
        $room = $rooms[array_rand($rooms)];

        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::GET_SINGLE_ROOM_URL, $room->getRoomID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer ' . $this->adminUserToken]
        );

        $requestResponse = $this->client->getResponse();
        $responseData = json_decode($requestResponse->getContent(), true);
        $payload = $responseData['payload'];

        self::assertEquals($room->getRoomID(), $payload['roomID']);
        self::assertEquals($room->getRoom(), $payload['roomName']);
    }

    public function test_regular_user_can_get_rooms(): void
    {
        /** @var Room $room */
        $rooms = $this->roomRepository->findAll();
        $room = $rooms[array_rand($rooms)];

        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::GET_SINGLE_ROOM_URL, $room->getRoomID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer ' . $this->regularUserToken]
        );

        self::assertResponseIsSuccessful();
    }

    public function test_admin_can_get_rooms(): void
    {
        /** @var Room $room */
        $rooms = $this->roomRepository->findAll();
        $room = $rooms[array_rand($rooms)];

        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::GET_SINGLE_ROOM_URL, $room->getRoomID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer ' . $this->adminUserToken]
        );

        self::assertResponseIsSuccessful();
    }

    public function test_user_not_logged_in_cannot_get_rooms(): void
    {
        /** @var Room $room */
        $rooms = $this->roomRepository->findAll();
        $room = $rooms[array_rand($rooms)];

        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::GET_SINGLE_ROOM_URL, $room->getRoomID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json']
        );

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }
}
