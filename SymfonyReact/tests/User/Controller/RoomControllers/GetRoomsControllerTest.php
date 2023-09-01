<?php

namespace App\Tests\User\Controller\RoomControllers;

use App\ORM\DataFixtures\Core\RoomFixtures;
use App\ORM\DataFixtures\Core\UserDataFixtures;
use App\Tests\Traits\TestLoginTrait;
use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class GetRoomsControllerTest extends WebTestCase
{
    use TestLoginTrait;

    private const GET_USER_ROOMS_URL = '/HomeApp/api/user/user-rooms/all';

    private ?EntityManagerInterface $entityManager;

    private KernelBrowser $client;

    private string $adminUserToken;

    private User $adminUser;

    private User $regularUser;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->adminUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::ADMIN_USER_EMAIL_ONE]);
        $this->regularUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_ONE]);
        $this->adminUserToken = $this->setUserToken($this->client);
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
        parent::tearDown();
    }

    public function test_getting_rooms_admin(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            self::GET_USER_ROOMS_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer ' . $this->adminUserToken]
        );

        $requestResponse = $this->client->getResponse();
        $responseData = json_decode($requestResponse->getContent(), true);
        $payload = $responseData['payload'];

        self::assertCount(count(RoomFixtures::ROOMS), $payload);

        foreach ($payload as $room) {
            self::assertNotNull($room['roomID']);
            self::assertIsInt($room['roomID']);

            self::assertNotNull($room['roomName']);
            self::assertIsString($room['roomName']);
        }
    }

    public function test_getting_rooms_regular_user(): void
    {
        $userToken = $this->setUserToken(
            $this->client,
            $this->regularUser->getEmail(),
            UserDataFixtures::REGULAR_PASSWORD
        );
        $this->client->request(
            Request::METHOD_GET,
            self::GET_USER_ROOMS_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer ' . $userToken]
        );

        $requestResponse = $this->client->getResponse();
        $responseData = json_decode($requestResponse->getContent(), true);
        $payload = $responseData['payload'];

        self::assertCount(count(RoomFixtures::ROOMS), $payload);

        foreach ($payload as $room) {
            self::assertNotNull($room['roomID']);
            self::assertIsInt($room['roomID']);

            self::assertNotNull($room['roomName']);
            self::assertIsString($room['roomName']);
        }
    }
}
