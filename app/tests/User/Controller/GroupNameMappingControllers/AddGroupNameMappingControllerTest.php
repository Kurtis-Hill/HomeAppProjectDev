<?php

namespace App\Tests\User\Controller\GroupNameMappingControllers;

use App\Controller\User\GroupMappingControllers\AddGroupMappingController;
use App\Controller\User\GroupsControllers\AddGroupController;
use App\DataFixtures\Core\UserDataFixtures;
use App\Entity\Authentication\GroupMapping;
use App\Entity\User\Group;
use App\Entity\User\User;
use App\Repository\Authentication\ORM\GroupMappingRepository;
use App\Repository\User\ORM\GroupRepositoryInterface;
use App\Repository\User\ORM\UserRepositoryInterface;
use App\Services\API\APIErrorMessages;
use App\Tests\Traits\TestLoginTrait;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AddGroupNameMappingControllerTest extends WebTestCase
{
    use TestLoginTrait;

    private const ADD_GROUP_NAME_MAPPING_URL = '/HomeApp/api/user/group-mapping/add';

    private ?EntityManagerInterface $entityManager;

    private KernelBrowser $client;

    private string $userToken;

    private User $adminUser;

    private User $regularUserTwo;

    private GroupRepositoryInterface $groupRepository;

    private UserRepositoryInterface $userRepository;

    private GroupMappingRepository $groupNameMappingRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->adminUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::ADMIN_USER_EMAIL_ONE]);
        $this->userToken = $this->setUserToken($this->client);
        $this->regularUserTwo = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_TWO]);
        $this->groupRepository = $this->entityManager->getRepository(Group::class);
        $this->userRepository = $this->entityManager->getRepository(User::class);
        $this->groupNameMappingRepository = $this->entityManager->getRepository(GroupMapping::class);
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
        parent::tearDown();
    }

    public function test_sending_malformed_request(): void
    {
        $this->client->request(
            Request::METHOD_POST,
            self::ADD_GROUP_NAME_MAPPING_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
            'sdffaf?sdfsd&sadfsdf&sdfa=3243'
        );

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $response = json_decode($this->client->getResponse()->getContent(), true);
        self::assertEquals([APIErrorMessages::FORMAT_NOT_SUPPORTED], $response['errors']);
    }

    /**
     * @dataProvider sendingWrongDataTypesDataProvider
     */
    public function test_sending_wrong_data_types(array $dataToSend, array $errorMessages): void
    {
        $jsonData = json_encode($dataToSend);
        $this->client->request(
            Request::METHOD_POST,
            self::ADD_GROUP_NAME_MAPPING_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
            $jsonData
        );

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $errors = $responseData['errors'];
        self::assertEquals($errorMessages, $errors);

        $title = $responseData['title'];
        self::assertEquals(AddGroupMappingController::BAD_REQUEST_NO_DATA_RETURNED, $title);
    }

    public function sendingWrongDataTypesDataProvider(): Generator
    {
        yield [
            'dataToSend' => [
                'userID' => 'string',
                'groupID' => 'string',
            ],
            'errorMessages' => [
                'userID must be a integer you have provided "string"',
                'groupID must be a integer you have provided "string"',
            ],
        ];

        yield [
            'dataToSend' => [
                'userID' => [],
                'groupID' => [],
            ],
            'errorMessages' => [
                'userID must be a integer you have provided array',
                'groupID must be a integer you have provided array',
            ],
        ];

        yield [
            'dataToSend' => [
                'userID' => true,
                'groupID' => false,
            ],
            'errorMessages' => [
                'userID must be a integer you have provided true',
                'groupID must be a integer you have provided false',
            ],
        ];
    }

    /**
     * @dataProvider missingDataDataProvider
     */
    public function test_sending_missing_data(array $dataToSend, array $errorMessages)
    {
        $jsonData = json_encode($dataToSend);
        $this->client->request(
            Request::METHOD_POST,
            self::ADD_GROUP_NAME_MAPPING_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
            $jsonData
        );

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $errors = $responseData['errors'];
        self::assertEquals($errorMessages, $errors);

        $title = $responseData['title'];
        self::assertEquals(AddGroupMappingController::BAD_REQUEST_NO_DATA_RETURNED, $title);
    }

    public function missingDataDataProvider(): Generator
    {
        yield [
            'dataToSend' => [
                'userID' => 1,
            ],
            'errorMessages' => [
                'groupID cannot be null',
            ],
        ];

        yield [
            'dataToSend' => [
                'groupID' => 1,
            ],
            'errorMessages' => [
                'userID cannot be null',
            ],
        ];
    }

    public function test_sending_userID_that_does_not_exist(): void
    {
        while (true) {
            $userID = random_int(1, 999999);
            $user = $this->userRepository->find($userID);
            if ($user === null) {
                break;
            }
        }

        /** @var Group[] $groupName */
        $groupName = $this->groupRepository->findAll();

        $jsonData = json_encode([
            'userID' => $userID,
            'groupID' => $groupName[0]->getGroupID(),
        ], JSON_THROW_ON_ERROR);

        $this->client->request(
            Request::METHOD_POST,
            self::ADD_GROUP_NAME_MAPPING_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
            $jsonData
        );

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $errors = $responseData['errors'];
        self::assertEquals([sprintf(APIErrorMessages::OBJECT_NOT_FOUND, 'User')], $errors);

        $title = $responseData['title'];

        self::assertEquals(AddGroupMappingController::BAD_REQUEST_NO_DATA_RETURNED, $title);
    }

    public function test_sending_groupID_that_does_not_exist(): void
    {
        while (true) {
            $groupID = random_int(1, 999999);
            $groupName = $this->groupRepository->find($groupID);
            if ($groupName === null) {
                break;
            }
        }

        /** @var \App\Entity\User\User[] $user */
        $user = $this->userRepository->findAll();

        $jsonData = json_encode([
            'userID' => $user[0]->getUserID(),
            'groupID' => $groupID,
        ], JSON_THROW_ON_ERROR);

        $this->client->request(
            Request::METHOD_POST,
            self::ADD_GROUP_NAME_MAPPING_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
            $jsonData
        );

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $errors = $responseData['errors'];
        self::assertEquals([sprintf(APIErrorMessages::OBJECT_NOT_FOUND, 'Group')], $errors);

        $title = $responseData['title'];

        self::assertEquals(AddGroupMappingController::BAD_REQUEST_NO_DATA_RETURNED, $title);
    }

    public function test_sending_group_name_mapping_request_for_group_name_that_is_already_mapped_to_user(): void
    {
        $adminGroupRegularUserIsApartOf = $this->groupRepository->findOneBy(['groupName' => UserDataFixtures::ADMIN_GROUP_ONE]);

        $jsonData = json_encode([
            'userID' => $this->regularUserTwo->getUserID(),
            'groupID' => $adminGroupRegularUserIsApartOf->getGroupID(),
        ], JSON_THROW_ON_ERROR);

        $this->client->request(
            Request::METHOD_POST,
            self::ADD_GROUP_NAME_MAPPING_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
            $jsonData
        );

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $errors = $responseData['errors'];
        self::assertEquals([GroupMapping::GROUP_NAME_MAPPING_EXISTS], $errors);

        $title = $responseData['title'];

        self::assertEquals(AddGroupMappingController::BAD_REQUEST_NO_DATA_RETURNED, $title);
    }

    public function test_sending_group_name_mapping_request_for_group_name_regular_user_doesnt_belong_to(): void
    {
        /** @var \App\Entity\User\Group[] $groupsNotApartOf */
        $groupsNotApartOf = $this->groupRepository->findGroupsUserIsNotApartOf($this->regularUserTwo);

        $regularUserToAddGroupNameToo = $this->userRepository->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_ONE]);

        $jsonData = json_encode([
            'userID' => $regularUserToAddGroupNameToo->getUserID(),
            'groupID' => $groupsNotApartOf[0]->getGroupID(),
        ], JSON_THROW_ON_ERROR);

        $userToken = $this->setUserToken(
            $this->client,
            $this->regularUserTwo->getEmail(),
            UserDataFixtures::REGULAR_PASSWORD
        );

        $this->client->request(
            Request::METHOD_POST,
            self::ADD_GROUP_NAME_MAPPING_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $userToken],
            $jsonData
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $title = $responseData['title'];
        self::assertEquals(AddGroupController::NOT_AUTHORIZED_TO_BE_HERE, $title);

    }

    public function test_admin_can_add_user_to_group_doesnt_belong_to(): void
    {
        /** @var \App\Entity\User\Group[] $groupsNotApartOf */
        $groupsNotApartOf = $this->groupRepository->findGroupsUserIsNotApartOf($this->regularUserTwo);

        $regularUserToAddGroupNameToo = $this->userRepository->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_ONE]);

        $jsonData = json_encode([
            'userID' => $regularUserToAddGroupNameToo->getUserID(),
            'groupID' => $groupsNotApartOf[0]->getGroupID(),
        ], JSON_THROW_ON_ERROR);

        $this->client->request(
            Request::METHOD_POST,
            self::ADD_GROUP_NAME_MAPPING_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
            $jsonData
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $title = $responseData['title'];
        self::assertEquals(AddGroupMappingController::REQUEST_SUCCESSFUL, $title);

        $groupNameMapping = $this->groupNameMappingRepository->findOneBy([
            'user' => $regularUserToAddGroupNameToo->getUserID(),
            'groupID' => $groupsNotApartOf[0]->getGroupID(),
        ]);

        self::assertNotNull($groupNameMapping);

        $payload = $responseData['payload'];

        self::assertEquals($groupNameMapping->getGroupMappingID(), $payload['groupMappingID']);
        self::assertArrayHasKey('user', $payload);
        self::assertArrayHasKey('group', $payload);
    }

    public function test_regular_user_can_add_another_user_to_own_group(): void
    {
        $regularUserToAddUserToo = $this->userRepository->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_ONE]);

        $jsonData = json_encode([
            'userID' => $this->regularUserTwo->getUserID(),
            'groupID' => $regularUserToAddUserToo->getGroup()->getGroupID(),
        ], JSON_THROW_ON_ERROR);

        $userToken = $this->setUserToken(
            $this->client,
            $regularUserToAddUserToo->getEmail(),
            UserDataFixtures::REGULAR_PASSWORD
        );

        $this->client->request(
            Request::METHOD_POST,
            self::ADD_GROUP_NAME_MAPPING_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $userToken],
            $jsonData
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $title = $responseData['title'];
        self::assertEquals(AddGroupMappingController::REQUEST_SUCCESSFUL, $title);

        $groupNameMapping = $this->groupNameMappingRepository->findOneBy([
            'user' => $this->regularUserTwo->getUserID(),
            'groupID' => $regularUserToAddUserToo->getGroup(),
        ]);

        self::assertNotNull($groupNameMapping);

        $payload = $responseData['payload'];

        self::assertEquals($groupNameMapping->getGroupMappingID(), $payload['groupMappingID']);
        self::assertArrayHasKey('user', $payload);
        self::assertArrayHasKey('group', $payload);
    }

    /**
     * @dataProvider wrongHttpsMethodDataProvider
     */
    public function test_using_wrong_http_method(string $httpVerb): void
    {
        $this->client->request(
            $httpVerb,
            self::ADD_GROUP_NAME_MAPPING_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );

        self::assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $this->client->getResponse()->getStatusCode());
    }

    public function wrongHttpsMethodDataProvider(): Generator
    {
        yield [Request::METHOD_GET];
        yield [Request::METHOD_PUT];
        yield [Request::METHOD_PATCH];
        yield [Request::METHOD_DELETE];
    }
}
