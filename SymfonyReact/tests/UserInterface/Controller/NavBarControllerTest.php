<?php

namespace App\Tests\UserInterface\Controller;

use App\ORM\DataFixtures\Core\RoomFixtures;
use App\ORM\DataFixtures\Core\UserDataFixtures;
use App\Authentication\Controller\SecurityController;
use App\Authentication\Entity\GroupMapping;
use App\Common\API\HTTPStatusCodes;
use App\Devices\Entity\Devices;
use App\Tests\Traits\TestLoginTrait;
use App\User\Entity\Room;
use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use JsonException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class NavBarControllerTest extends WebTestCase
{
    use TestLoginTrait;

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

        $this->userToken = $this->setUserToken($this->client);
    }

    /**
     * @dataProvider getNavBarDataUserDataProvider
     */
    public function test_get_navbar_data_response_admin_user(string $email, string $password, int $groupCount): void
    {
        $userRepository = $this->entityManager->getRepository(User::class);
        /** @var User $testUser */
        $testUser = $userRepository->findOneBy(['email' => $email]);

        $userToken = $this->setUserToken($this->client, $email, $password);

        /** @var Room[] $userRooms */
        $userRooms = $this->entityManager->getRepository(Room::class)->findAll();

        /** @var Devices[] $userDevices */
        $userDevices = $this->entityManager->getRepository(Devices::class)->findAllUsersDevicesByGroupId($testUser->getAssociatedGroupIDs());

        $this->client->request(
            Request::METHOD_GET,
            self::NAVBAR_DATA_URL,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $userToken, 'CONTENT_TYPE' => 'application/json'],
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true)['payload'];

        foreach ($responseData as $response) {
            $userLinks = $response['listItemLinks'];
            self::assertEmpty($response['errors'], 'errors is not empty');
            self::assertNotEmpty($userLinks, 'userLinks is empty');

            foreach ($userLinks as $links) {
                self::assertIsString($links['displayName'], 'Device name is not string');
                self::assertIsString($links['link'], 'device link is not string');
            }
            if ($response['header'] === 'View Devices') {
                self::assertEquals('microchip', $response['icon'], 'device icon is wrong');
                self::assertEquals('devices', $response['itemName']);
                continue;
            }


            if ($response['header'] === 'View Rooms') {
                self::assertEquals('person-booth', $response['icon'], 'room icon is wrong');
                self::assertEquals('rooms', $response['itemName']);
                continue;
            }

            if ($response['header'] === 'View Groups') {
                self::assertEquals('users', $response['icon'], 'group icon is wrong');
                self::assertEquals('groups', $response['itemName']);
                continue;
            }

            self::fail('header is not valid');
        }

        $countMessage = '%s count is wrong';

        self::assertCount(count($userRooms), $responseData[2]['listItemLinks'], sprintf($countMessage, 'rooms'));
        self::assertCount(count($userDevices), $responseData[0]['listItemLinks'], sprintf($countMessage, 'device'));
        self::assertCount($groupCount, $responseData[1]['listItemLinks'], sprintf($countMessage, 'group name'));
        self::assertSameSize(RoomFixtures::ROOMS, $responseData[2]['listItemLinks'], sprintf($countMessage, 'room'));
        self::assertCount($groupCount, $responseData[1]['listItemLinks'], sprintf($countMessage, 'group'));

        self::assertResponseStatusCodeSame(HTTPStatusCodes::HTTP_OK);
    }

    public function getNavBarDataUserDataProvider(): Generator
    {
        yield [
            'email' => UserDataFixtures::ADMIN_USER_EMAIL_ONE,
            'password' => UserDataFixtures::ADMIN_PASSWORD,
            'groupCount' => count(UserDataFixtures::ALL_GROUPS),
        ];

        yield [
            'email' => UserDataFixtures::ADMIN_USER_EMAIL_TWO,
            'password' => UserDataFixtures::ADMIN_PASSWORD,
            'groupCount' => count(UserDataFixtures::ALL_GROUPS),
        ];

        yield [
            'email' => UserDataFixtures::REGULAR_USER_EMAIL_ONE,
            'password' => UserDataFixtures::REGULAR_PASSWORD,
            'groupCount' => 2,
        ];

        yield [
            'email' => UserDataFixtures::REGULAR_USER_EMAIL_TWO,
            'password' => UserDataFixtures::REGULAR_PASSWORD,
            'groupCount' => count(UserDataFixtures::GROUPS_SECOND_REGULAR_USER_IS_ADDED_TO),
        ];
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
