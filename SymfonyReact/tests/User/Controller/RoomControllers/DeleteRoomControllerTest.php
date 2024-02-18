<?php

namespace App\Tests\User\Controller\RoomControllers;

use App\Common\API\APIErrorMessages;
use App\Common\API\CommonURL;
use App\ORM\DataFixtures\Core\UserDataFixtures;
use App\Tests\Traits\TestLoginTrait;
use App\User\Controller\RoomControllers\DeleteRoomController;
use App\User\Entity\Room;
use App\User\Entity\User;
use App\User\Repository\ORM\RoomRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DeleteRoomControllerTest extends WebTestCase
{
    use TestLoginTrait;

    private const DELETE_ROOM_URL = CommonURL::USER_HOMEAPP_API_URL . 'user-rooms/%d/delete';

    private ?EntityManagerInterface $entityManager;

    private KernelBrowser $client;

    private string $userToken;

    private User $adminUser;

    private User $regularUserTwo;

    private RoomRepositoryInterface $roomRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->adminUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::ADMIN_USER_EMAIL_ONE]);
        $this->regularUserTwo = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_TWO]);
        $this->roomRepository = $this->entityManager->getRepository(Room::class);
        $this->userToken = $this->setUserToken($this->client);
    }

    public function test_deleting_room_doesnt_exist(): void
    {
        while (true) {
            $roomID = random_int(1, 10000);
            /** @var Room $room */
            $room = $this->roomRepository->findOneBy(['roomID' => $roomID]);
            if ($room === null) {
                break;
            }
        }

        $this->client->request(
            Request::METHOD_DELETE,
            sprintf(self::DELETE_ROOM_URL, $roomID),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_deleting_room_regular_user(): void
    {
        $rooms = $this->roomRepository->findAll();

        $room = $rooms[0];

        $userToken = $this->setUserToken($this->client, $this->regularUserTwo->getEmail(), UserDataFixtures::REGULAR_PASSWORD);

        $this->client->request(
            Request::METHOD_DELETE,
            sprintf(self::DELETE_ROOM_URL, $room->getRoomID()),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $userToken, 'CONTENT_TYPE' => 'application/json'],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $title = $responseData['title'];
        self::assertEquals(DeleteRoomController::NOT_AUTHORIZED_TO_BE_HERE, $title);

        $errors = $responseData['errors'];
        self::assertEquals([APIErrorMessages::ACCESS_DENIED], $errors);
    }

    public function test_deleting_room_admin(): void
    {
        $rooms = $this->roomRepository->findAll();

        $room = $rooms[0];

        $this->client->request(
            Request::METHOD_DELETE,
            sprintf(self::DELETE_ROOM_URL, $room->getRoomID()),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $title = $responseData['title'];
        self::assertEquals(DeleteRoomController::REQUEST_SUCCESSFUL, $title);

        $payload = $responseData['payload'];
        self::assertEquals([sprintf(DeleteRoomController::DELETED_ROOM_SUCCESSFULLY, $room->getRoomID())], $payload);
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
        parent::tearDown();
    }
}
