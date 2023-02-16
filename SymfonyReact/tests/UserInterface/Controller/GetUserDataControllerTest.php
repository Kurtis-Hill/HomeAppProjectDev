<?php

namespace App\Tests\UserInterface\Controller;

use App\Authentication\Entity\GroupNameMapping;
use App\Common\API\HTTPStatusCodes;
use App\ORM\DataFixtures\Core\RoomFixtures;
use App\ORM\DataFixtures\Core\UserDataFixtures;
use App\Tests\Traits\TestLoginTrait;
use App\User\Entity\Room;
use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
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

    public function test_get_user_data_response(): void
    {
        $userRepository = $this->entityManager->getRepository(User::class);
        /** @var User $testUser */
        $testUser = $userRepository->findOneBy(['email' => UserDataFixtures::ADMIN_USER_EMAIL_ONE]);

        /** @var Room[] $userRooms */
        $userRooms = $this->entityManager->getRepository(Room::class)->getAllUserRoomsByGroupId($testUser->getAssociatedGroupNameIds(), 1);
        $this->client->request(
            Request::METHOD_GET,
            self::GET_USER_DATA_URL,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true)['payload'];

        self::assertSameSize(RoomFixtures::ROOMS, $responseData['userRooms']);
        self::assertSameSize($userRooms, $responseData['userRooms']);
        self::assertSameSize($testUser->getAssociatedGroupNameIds(), $responseData['userGroups']);
        self::assertResponseStatusCodeSame(HTTPStatusCodes::HTTP_OK);

        /** @var Room $room */
        foreach ($userRooms as $room) {
            foreach ($responseData['userRooms'] as $userRoom) {
                if ($userRoom['roomID'] === $room->getRoomID()) {
                    self::assertEquals($room->getRoom(), $userRoom['roomName'], 'room name wrong');
//                    self::assertEquals($room->getGroupNameID()->getGroupNameID(), $userRoom['groupNameID'], 'room type wrong');
                    $passed = true;
                    continue;
                }
                if (!isset($passed)) {
                    self::fail('room not found');
                }
            }
        }

        foreach ($testUser->getGroupNameMappings() as $groupNameObject) {
            foreach ($responseData['userGroups'] as $userGroup) {
                if ($userGroup['groupNameID'] === $groupNameObject->getGroupNameID()) {
                    self::assertEquals($groupNameObject->getGroupName(), $userGroup['groupName'], 'group name wrong');
                    $passed = true;
                    continue;
                }
                if (!isset($passed)) {
                    self::fail('group not found');
                }
            }
        }
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
