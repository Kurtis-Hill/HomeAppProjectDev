<?php

namespace App\Tests\Controller\User\GroupsController;

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

class AddGroupControllerTest extends WebTestCase
{
    use TestLoginTrait;

    private const ADD_NEW_GROUP_URL = '/HomeApp/api/user/user-groups';

    private ?EntityManagerInterface $entityManager;

    private KernelBrowser $client;

    private string $userToken;

    private User $adminUser;

    private GroupRepositoryInterface $groupNameRepository;

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
        $this->groupNameRepository = $this->entityManager->getRepository(Group::class);
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
            self::ADD_NEW_GROUP_URL,
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
     * @dataProvider invalidDataTypesDataProvider
     */
    public function test_sending_invalid_data_types(mixed $groupName, array $message): void
    {
        $formRequestData = [
            'groupName' => $groupName,
        ];

        $jsonData = json_encode($formRequestData);

        $this->client->request(
            Request::METHOD_POST,
            self::ADD_NEW_GROUP_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
            $jsonData
        );

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $response = json_decode($this->client->getResponse()->getContent(), true);

        self::assertEquals($message, $response['errors']);
        self::assertEquals(AddGroupController::BAD_REQUEST_NO_DATA_RETURNED, $response['title']);
    }

    public function invalidDataTypesDataProvider(): Generator
    {
        yield [
            'groupName' => [],
            'message' => ['groupName must be a string you have provided array']
        ];

        yield [
            'groupName' => 123,
            'message' => ['groupName must be a string you have provided 123']
        ];

        yield [
            'groupName' => 123.123,
            'message' => ['groupName must be a string you have provided 123.123']
        ];

        yield [
            'groupName' => true,
            'message' => ['groupName must be a string you have provided true']
        ];

        yield [
            'groupName' => false,
            'message' => ['groupName must be a string you have provided false']
        ];
    }

    public function test_admin_can_add_new_group(): void
    {
        $groupNameForRequest = 'newGroupNameUnique';
        $formRequestData = [
            'groupName' => $groupNameForRequest,
        ];

        $jsonData = json_encode($formRequestData);

        $this->client->request(
            Request::METHOD_POST,
            self::ADD_NEW_GROUP_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
            $jsonData
        );

        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $response = json_decode($this->client->getResponse()->getContent(), true);

        self::assertEquals(AddGroupController::REQUEST_ACCEPTED_SUCCESS_CREATED, $response['title']);

        /** @var \App\Entity\User\Group $newGroupName */
        $newGroupName = $this->groupNameRepository->findOneBy(['groupName' => 'newGroupNameUnique']);

        self::assertNotNull($newGroupName);
        self::assertEquals($groupNameForRequest, $newGroupName->getGroupName());
    }

    public function test_regular_user_can_add_new_group(): void
    {
        $userToken = $this->setUserToken($this->client, UserDataFixtures::REGULAR_USER_EMAIL_ONE, UserDataFixtures::REGULAR_PASSWORD);

        $groupNameForRequest = 'newGroupNameUnique';
        $formRequestData = [
            'groupName' => $groupNameForRequest,
        ];

        $jsonData = json_encode($formRequestData);

        $this->client->request(
            Request::METHOD_POST,
            self::ADD_NEW_GROUP_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $userToken],
            $jsonData
        );

        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $response = json_decode($this->client->getResponse()->getContent(), true);

        self::assertEquals(AddGroupController::REQUEST_ACCEPTED_SUCCESS_CREATED, $response['title']);

        /** @var \App\Entity\User\Group $newGroupName */
        $newGroupName = $this->groupNameRepository->findOneBy(['groupName' => 'newGroupNameUnique']);

        self::assertNotNull($newGroupName);
        self::assertEquals($groupNameForRequest, $newGroupName->getGroupName());
    }

    public function test_regular_user_gets_added_to_group_name_entity(): void
    {
        $userToken = $this->setUserToken(
            $this->client,
            UserDataFixtures::REGULAR_USER_EMAIL_ONE,
            UserDataFixtures::REGULAR_PASSWORD
        );

        $groupNameForRequest = 'newGroupNameUnique';
        $formRequestData = [
            'groupName' => $groupNameForRequest,
        ];

        $jsonData = json_encode($formRequestData);

        $this->client->request(
            Request::METHOD_POST,
            self::ADD_NEW_GROUP_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $userToken],
            $jsonData
        );

        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $response = json_decode($this->client->getResponse()->getContent(), true);

        self::assertEquals(AddGroupController::REQUEST_ACCEPTED_SUCCESS_CREATED, $response['title']);

        /** @var Group $newGroupName */
        $newGroupName = $this->groupNameRepository->findOneBy(['groupName' => $groupNameForRequest]);
        self::assertNotNull($newGroupName);
        self::assertEquals($groupNameForRequest, $newGroupName->getGroupName());

        /** @var User $regularUser */
        $regularUser = $this->userRepository->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_ONE]);
        self::assertNotNull($regularUser);

        $groupNameMappingShouldOfBeenCreated = $this->groupNameMappingRepository->findBy(
            [
                'groupID' => $newGroupName,
                'user' => $regularUser
            ]
        );
        self::assertNotNull($groupNameMappingShouldOfBeenCreated);
    }

    public function test_full_response_message_from_successful_request(): void
    {
        $groupNameForRequest = 'newGroupNameUnique';
        $formRequestData = [
            'groupName' => $groupNameForRequest,
        ];

        $jsonData = json_encode($formRequestData);

        $this->client->request(
            Request::METHOD_POST,
            self::ADD_NEW_GROUP_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
            $jsonData
        );

        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $response = json_decode($this->client->getResponse()->getContent(), true);

        $payload = $response['payload'];
        self::assertEquals(AddGroupController::REQUEST_ACCEPTED_SUCCESS_CREATED, $response['title']);
        self::assertArrayHasKey('groupID', $payload);
        self::assertEquals($groupNameForRequest, $payload['groupName']);
    }

//    /**
//     * @dataProvider wrongHttpsMethodDataProvider
//     */
//    public function test_using_wrong_http_method(string $httpVerb): void
//    {
//        $this->client->request(
//            $httpVerb,
//            self::ADD_NEW_GROUP_URL,
//            [],
//            [],
//            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
//        );
//
//        self::assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $this->client->getResponse()->getStatusCode());
//    }

    public function wrongHttpsMethodDataProvider(): Generator
    {
        yield [Request::METHOD_GET];
        yield [Request::METHOD_PUT];
        yield [Request::METHOD_PATCH];
        yield [Request::METHOD_DELETE];
    }
}
