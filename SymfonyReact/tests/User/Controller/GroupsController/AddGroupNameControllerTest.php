<?php

namespace App\Tests\User\Controller\GroupsController;

use App\Authentication\Entity\GroupNameMapping;
use App\Authentication\Repository\ORM\GroupNameMappingRepository;
use App\Common\API\APIErrorMessages;
use App\ORM\DataFixtures\Core\UserDataFixtures;
use App\Tests\Traits\TestLoginTrait;
use App\User\Controller\GroupsControllers\AddGroupNameController;
use App\User\Entity\GroupNames;
use App\User\Entity\User;
use App\User\Repository\ORM\GroupNameRepositoryInterface;
use App\User\Repository\ORM\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AddGroupNameControllerTest extends WebTestCase
{
    use TestLoginTrait;

    private const ADD_NEW_GROUP_URL = '/HomeApp/api/user/user-groups/add';

    private ?EntityManagerInterface $entityManager;

    private KernelBrowser $client;

    private string $userToken;

    private User $adminUser;

    private GroupNameRepositoryInterface $groupNameRepository;

    private UserRepositoryInterface $userRepository;

    private GroupNameMappingRepository $groupNameMappingRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->adminUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::ADMIN_USER_EMAIL_ONE]);
        $this->userToken = $this->setUserToken($this->client);
        $this->groupNameRepository = $this->entityManager->getRepository(GroupNames::class);
        $this->userRepository = $this->entityManager->getRepository(User::class);
        $this->groupNameMappingRepository = $this->entityManager->getRepository(GroupNameMapping::class);
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
        self::assertEquals(AddGroupNameController::BAD_REQUEST_NO_DATA_RETURNED, $response['title']);
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

        self::assertEquals(AddGroupNameController::REQUEST_ACCEPTED_SUCCESS_CREATED, $response['title']);

        /** @var GroupNames $newGroupName */
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

        self::assertEquals(AddGroupNameController::REQUEST_ACCEPTED_SUCCESS_CREATED, $response['title']);

        /** @var GroupNames $newGroupName */
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

        self::assertEquals(AddGroupNameController::REQUEST_ACCEPTED_SUCCESS_CREATED, $response['title']);

        /** @var GroupNames $newGroupName */
        $newGroupName = $this->groupNameRepository->findOneBy(['groupName' => $groupNameForRequest]);

        self::assertNotNull($newGroupName);
        self::assertEquals($groupNameForRequest, $newGroupName->getGroupName());

        /** @var User $regularUser */
        $regularUser = $this->userRepository->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_ONE]);
        self::assertNotNull($regularUser);

        $groupNameMappingShouldOfBeenCreated = $this->groupNameMappingRepository->findBy(
            [
                'groupName' => $newGroupName,
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
        self::assertEquals(AddGroupNameController::REQUEST_ACCEPTED_SUCCESS_CREATED, $response['title']);
        self::assertArrayHasKey('groupNameID', $payload);
        self::assertEquals($groupNameForRequest, $payload['groupName']);
    }

    /**
     * @dataProvider wrongHttpsMethodDataProvider
     */
    public function test_using_wrong_http_method(string $httpVerb): void
    {
        $this->client->request(
            $httpVerb,
            self::ADD_NEW_GROUP_URL,
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
