<?php

namespace App\Tests\UserInterface\Controller;

use App\API\HTTPStatusCodes;
use App\Authentication\Controller\SecurityController;
use App\Authentication\Entity\GroupNameMapping;
use App\DataFixtures\Core\RoomFixtures;
use App\DataFixtures\Core\UserDataFixtures;
use App\Devices\Entity\Devices;
use App\User\Entity\Room;
use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use JsonException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class NavBarControllerTest extends WebTestCase
{
    private const NAVBAR_DATA_URL =  '/HomeApp/api/user/navbar/navbar-data';

    private ?string $userToken = null;

    private ?EntityManagerInterface $entityManager;

    private KernelBrowser $client;


    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        try {
            $this->userToken = $this->setUserToken();
        } catch (JsonException $e) {
            error_log($e);
        }
    }

    private function setUserToken(bool $forceToken = false): string
    {
        if ($this->userToken === null || $forceToken === true) {
            $this->client->request(
                Request::METHOD_POST,
                SecurityController::API_USER_LOGIN,
                [],
                [],
                ['CONTENT_TYPE' => 'application/json'],
                '{"username":"'.UserDataFixtures::ADMIN_USER.'","password":"'.UserDataFixtures::ADMIN_PASSWORD.'"}'
            );

            $requestResponse = $this->client->getResponse();
            $responseData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

            return $responseData['token'];
        }

        return $this->userToken;
    }


    public function test_navbar_data_response(): void
    {
        $userRepository = $this->entityManager->getRepository(User::class);
        $testUser = $userRepository->findOneBy(['email' => UserDataFixtures::ADMIN_USER]);

        $groupNameMappingEntities = $this->entityManager->getRepository(GroupNameMapping::class)->getAllGroupMappingEntitiesForUser($testUser);
        $testUser->setUserGroupMappingEntities($groupNameMappingEntities);

        $userRooms = $this->entityManager->getRepository(Room::class)->getAllUserRoomsByGroupId($testUser->getGroupNameIds());
        $userDevices = $this->entityManager->getRepository(Devices::class)->getAllUsersDevicesByGroupId($testUser->getGroupNameIds());

        $this->client->request(
            Request::METHOD_GET,
            self::NAVBAR_DATA_URL,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true)['payload'];

        $countMessage = 'user %s count wrong';

        self::assertCount(count($userRooms), $responseData['rooms'], sprintf($countMessage, 'rooms'));
        self::assertCount(count($userDevices), $responseData['devices'], sprintf($countMessage, 'device'));
        self::assertCount(count($testUser->getGroupNameIds()), $responseData['groupNames'], sprintf($countMessage, 'group name'));

        self::assertSameSize(RoomFixtures::ROOMS, $responseData['rooms']);
        self::assertSameSize(UserDataFixtures::USER_ACCOUNTS, $responseData['groupNames']);

        self::assertEquals(HTTPStatusCodes::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function test_navbar_data_response_wrong_token(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            self::NAVBAR_DATA_URL,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken . '1'],
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals('Invalid JWT Token', $responseData['message']);
        self::assertEquals(HTTPStatusCodes::HTTP_UNAUTHORISED, $this->client->getResponse()->getStatusCode());
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
        parent::tearDown();
    }
}
