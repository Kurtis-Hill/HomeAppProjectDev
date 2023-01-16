<?php

namespace App\Tests\UserInterface\Controller;

use App\Doctrine\DataFixtures\Core\RoomFixtures;
use App\Doctrine\DataFixtures\Core\UserDataFixtures;
use App\Authentication\Controller\SecurityController;
use App\Authentication\Entity\GroupNameMapping;
use App\Common\API\HTTPStatusCodes;
use App\Devices\Entity\Devices;
use App\Tests\Traits\TestLoginTrait;
use App\User\Entity\Room;
use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
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

    public function test_get_navbar_data_response(): void
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

        foreach ($responseData as $response) {
            $userLinks = $response['listItemLinks'];

            self::assertEmpty($response['errors'], 'errors is not empty');
            self::assertNotEmpty($userLinks, 'userLinks is empty');
            foreach ($userLinks as $links) {
                self::assertIsString($links['displayName'], 'Device name is not string');
                self::assertIsString($links['link'], 'device link is not string');
            }
            if ($response['header'] === 'View Devices') {
                $userDevicePassed = true;
                self::assertEquals('microchip', $response['icon'], 'device icon is wrong');
                self::assertEquals('devices', $response['itemName']);
            }


            if ($response['header'] === 'View Rooms') {
                $userRoomsPassed = true;
                self::assertEquals('person-booth', $response['icon'], 'room icon is wrong');
                self::assertEquals('rooms', $response['itemName']);
            }

            if ($response['header'] === 'View Groups') {
                $userGroupsPassed = true;
                self::assertEquals('users', $response['icon'], 'group icon is wrong');
                self::assertEquals('groups', $response['itemName']);
            }
        }

        if (!isset($userDevicePassed)) {
            self::fail('User devices not found');
        }
        if (!isset($userRoomsPassed)) {
            self::fail('User rooms not found');
        }
        if (!isset($userGroupsPassed)) {
            self::fail('User groups not found');
        }

        $countMessage = '%s count is wrong';

        self::assertCount(count($userRooms), $responseData[2]['listItemLinks'], sprintf($countMessage, 'rooms'));
        self::assertCount(count($userDevices), $responseData[0]['listItemLinks'], sprintf($countMessage, 'device'));
        self::assertCount(count($testUser->getGroupNameIds()), $responseData[1]['listItemLinks'], sprintf($countMessage, 'group name'));
        self::assertSameSize(RoomFixtures::ROOMS, $responseData[2]['listItemLinks'], sprintf($countMessage, 'room'));
        self::assertSameSize(UserDataFixtures::USER_GROUPS, $responseData[1]['listItemLinks'], sprintf($countMessage, 'group'));

        self::assertResponseStatusCodeSame(HTTPStatusCodes::HTTP_OK);
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
