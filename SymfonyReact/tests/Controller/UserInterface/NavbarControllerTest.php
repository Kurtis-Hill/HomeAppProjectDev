<?php


namespace App\Tests\Controller\UserInterface;


use App\API\HTTPStatusCodes;
use App\Controller\Core\SecurityController;
use App\DataFixtures\Core\RoomFixtures;
use App\DataFixtures\Core\UserDataFixtures;
use App\DataFixtures\ESP8266\ESP8266DeviceFixtures;
use App\Entity\Core\GroupnNameMapping;
use App\Entity\Core\Room;
use App\Entity\Core\User;
use App\Entity\Devices\Devices;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class NavbarControllerTest extends WebTestCase
{
    private const NAVBAR_DATA_URL = '/HomeApp/api/navbar/navbar-data';

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


    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        try {
            $this->setUserToken();
        } catch (\JsonException $e) {
            error_log($e);
        }
    }

    /**
     * @return void
     * @throws \JsonException
     */
    private function setUserToken()
    {
        if ($this->userToken === null) {
            $this->client->request(
                'POST',
                SecurityController::API_USER_LOGIN,
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

    //navBarData method
    public function test_navbar_data_response()
    {
        $userRepository = $this->entityManager->getRepository(User::class);
        $testUser = $userRepository->findOneBy(['email' => UserDataFixtures::ADMIN_USER]);

        $groupNameMappingEntities = $this->entityManager->getRepository(GroupnNameMapping::class)->getAllGroupMappingEntitiesForUser($testUser);
        $testUser->setUserGroupMappingEntities($groupNameMappingEntities);

        $userRooms = $this->entityManager->getRepository(Room::class)->getAllUserRoomsByGroupId($testUser->getGroupNameIds());

        $userDevices = $this->entityManager->getRepository(Devices::class)->getAllUsersDevicesByGroupId($testUser->getGroupNameIds());

        $this->client->request(
            'GET',
            self::NAVBAR_DATA_URL,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true)['responseData'];

        $countMessage = 'user %s count wrong';

        self::assertCount(count($userRooms), $responseData['rooms'], sprintf($countMessage, 'rooms'));
        self::assertCount(count($userDevices), $responseData['devices'], sprintf($countMessage, 'device'));
        self::assertCount(count($testUser->getGroupNameIds()), $responseData['groupNames'], sprintf($countMessage, 'group name'));

        // +1 for the apiLoginTestDevice
        self::assertCount(count(ESP8266DeviceFixtures::PERMISSION_CHECK_DEVICES) + 1, $responseData['devices']);

        self::assertSameSize(RoomFixtures::ROOMS, $responseData['rooms']);
        self::assertSameSize(UserDataFixtures::USER_ACCOUNTS, $responseData['groupNames']);

        self::assertEquals(HTTPStatusCodes::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function test_navbar_data_response_wrong_token()
    {
        $this->client->request(
            'GET',
            self::NAVBAR_DATA_URL,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken . '1'],
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals('Invalid JWT Token', $responseData['message']);
        self::assertEquals(HTTPStatusCodes::HTTP_UNAUTHORISED, $this->client->getResponse()->getStatusCode());
    }
    //END of navBarData
}
