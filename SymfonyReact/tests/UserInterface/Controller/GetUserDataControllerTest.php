<?php

namespace App\Tests\UserInterface\Controller;

use App\Authentication\Entity\GroupNameMapping;
use App\Common\API\HTTPStatusCodes;
use App\ORM\DataFixtures\Core\RoomFixtures;
use App\ORM\DataFixtures\Core\UserDataFixtures;
use App\Tests\Traits\TestLoginTrait;
use App\User\Entity\GroupNames;
use App\User\Entity\Room;
use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class GetUserDataControllerTest extends WebTestCase
{
    use TestLoginTrait;

    private const GET_USER_DATA_URL = '/HomeApp/api/user/user-data/get';

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
     * @dataProvider getUserDataResponseDataProvider
     */
    public function test_get_user_data_response(string $email, string $password, int $groupNameNumber): void
    {
        $userRepository = $this->entityManager->getRepository(User::class);
        /** @var User $testUser */
        $testUser = $userRepository->findOneBy(['email' => $email]);

        /** @var Room[] $userRooms */
        $userRooms = $this->entityManager->getRepository(Room::class)->findAll();

        $userToken = $this->setUserToken(
            $this->client,
            $email,
            $password
        );
        $this->client->request(
            Request::METHOD_GET,
            self::GET_USER_DATA_URL,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $userToken, 'CONTENT_TYPE' => 'application/json'],
        );
        self::assertResponseStatusCodeSame(HTTPStatusCodes::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true)['payload'];

        self::assertSameSize(RoomFixtures::ROOMS, $responseData['userRooms']);
        self::assertSameSize($userRooms, $responseData['userRooms']);

        self::assertCount($groupNameNumber, $responseData['userGroups']);

        foreach ($userRooms as $room) {
            foreach ($responseData['userRooms'] as $userRoom) {
                if ($userRoom['roomID'] === $room->getRoomID()) {
                    self::assertEquals($room->getRoom(), $userRoom['roomName'], 'room name wrong');
                    $passed = true;
                    continue;
                }
                if (!isset($passed)) {
                    self::fail('room not found');
                }
            }
        }

        foreach ($testUser->getUserGroupMappingEntities() as $groupNameMappingObject) {
            /** @var GroupNames $groupName */
            $groupName = $groupNameMappingObject->getGroupName();
            foreach ($responseData['userGroups'] as $userGroup) {
                if ($userGroup['groupNameID'] === $groupName->getGroupNameID()) {
                    self::assertEquals($groupName->getGroupName(), $userGroup['groupName'], 'group name wrong');
                    $passed = true;
                    continue;
                }
                if (!isset($passed)) {
                    self::fail('group not found');
                }
            }
        }
    }

    public function getUserDataResponseDataProvider(): Generator
    {
        yield [
            'email' => UserDataFixtures::ADMIN_USER_EMAIL_ONE,
            'password' => UserDataFixtures::ADMIN_PASSWORD,
            'groupNameNumber' => count(UserDataFixtures::ALL_GROUPS),
        ];
        yield [
            'email' => UserDataFixtures::ADMIN_USER_EMAIL_TWO,
            'password' => UserDataFixtures::ADMIN_PASSWORD,
            'groupNameNumber' => count(UserDataFixtures::ALL_GROUPS),
        ];
        yield [
            'email' => UserDataFixtures::REGULAR_USER_EMAIL_ONE,
            'password' => UserDataFixtures::REGULAR_PASSWORD,
            'groupNameNumber' => 2,
        ];
        yield [
            'email' => UserDataFixtures::REGULAR_USER_EMAIL_TWO,
            'password' => UserDataFixtures::REGULAR_PASSWORD,
            'groupNameNumber' => count(UserDataFixtures::GROUPS_SECOND_REGULAR_USER_IS_ADDED_TO),
        ];
    }

    public function test_navbar_data_response_wrong_token(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            self::GET_USER_DATA_URL,
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
