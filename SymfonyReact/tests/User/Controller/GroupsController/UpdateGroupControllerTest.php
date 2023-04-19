<?php

namespace App\Tests\User\Controller\GroupsController;

use App\Authentication\Entity\GroupNameMapping;
use App\Authentication\Repository\ORM\GroupNameMappingRepository;
use App\Common\API\APIErrorMessages;
use App\ORM\DataFixtures\Core\UserDataFixtures;
use App\Tests\Traits\TestLoginTrait;
use App\User\Controller\GroupsControllers\AddGroupController;
use App\User\Controller\GroupsControllers\UpdateGroupController;
use App\User\Entity\GroupNames;
use App\User\Entity\User;
use App\User\Repository\ORM\GroupRepositoryInterface;
use App\User\Repository\ORM\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateGroupControllerTest extends WebTestCase
{
    use TestLoginTrait;

    private const UPDATE_GROUP_URL = '/HomeApp/api/user/user-groups/%s/update';

    private ?EntityManagerInterface $entityManager;

    private KernelBrowser $client;

    private string $userToken;

    private User $user;

    private User $regularUserTwo;

    private GroupRepositoryInterface $groupNameRepository;

    private UserRepositoryInterface $userRepository;

    private GroupNameMappingRepository $groupNameMappingRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::ADMIN_USER_EMAIL_ONE]);
        $this->regularUserTwo = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_TWO]);
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
        /** @var GroupNames[] $groups */
        $groups = $this->groupNameRepository->findAll();
        $group = $groups[0];

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_GROUP_URL, $group->getGroupID()),
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
     * @dataProvider wrongHttpsMethodDataProvider
     */
    public function test_using_wrong_http_method(string $httpVerb): void
    {
        $groups = $this->groupNameRepository->findAll();
        $group = $groups[0];
        $this->client->request(
            $httpVerb,
            sprintf(self::UPDATE_GROUP_URL, $group->getGroupID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );

        self::assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $this->client->getResponse()->getStatusCode());
    }

    public function wrongHttpsMethodDataProvider(): Generator
    {
        yield [Request::METHOD_POST];
        yield [Request::METHOD_GET];
        yield [Request::METHOD_DELETE];
    }

    /**
     * @dataProvider invalidDataTypesDataProvider
     */
    public function test_sending_invalid_data_types(mixed $groupName, array $message): void
    {
        /** @var GroupNames[] $groups */
        $groups = $this->groupNameRepository->findAll();
        $group = $groups[0];

        $formRequestData = [
            'groupName' => $groupName,
        ];

        $jsonData = json_encode($formRequestData);

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_GROUP_URL, $group->getGroupID()),
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

    public function test_regular_user_cannot_update_group_not_apart_of(): void
    {
        /** @var GroupNames[] $groupsUserNotApartOf */
        $groupsUserNotApartOf = $this->groupNameRepository->findGroupsUserIsNotApartOf($this->regularUserTwo);

        if (empty($groupsUserNotApartOf)) {
            self::fail('No groups found for user to update');
        }

        $group = $groupsUserNotApartOf[0];

        $formRequestData = [
            'groupName' => 'NewGroupName',
        ];

        $jsonData = json_encode($formRequestData);

        $userToken = $this->setUserToken($this->client,
            $this->regularUserTwo->getEmail(),
            UserDataFixtures::REGULAR_PASSWORD
        );

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_GROUP_URL, $group->getGroupID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $userToken],
            $jsonData
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        $response = json_decode($this->client->getResponse()->getContent(), true);

        self::assertEquals(UpdateGroupController::NOT_AUTHORIZED_TO_BE_HERE, $response['title']);

        $updatedGroup = $this->groupNameRepository->findOneBy(['groupID' => $group->getGroupID()]);

        self::assertEquals($group->getGroupName(), $updatedGroup->getGroupName());
    }

    public function test_admin_user_can_update_group_not_apart_of(): void
    {
        /** @var GroupNames[] $groupsUserNotApartOf */
        $groupsUserNotApartOf = $this->groupNameRepository->findGroupsUserIsNotApartOf($this->user);

        if (empty($groupsUserNotApartOf)) {
            self::fail('No groups found for user to update');
        }

        $group = $groupsUserNotApartOf[0];

        $formRequestData = [
            'groupName' => 'NewGroupName',
        ];

        $jsonData = json_encode($formRequestData);

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_GROUP_URL, $group->getGroupID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
            $jsonData
        );

        self::assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);

        $response = json_decode($this->client->getResponse()->getContent(), true);
        $payload = $response['payload'];

        self::assertEquals($formRequestData['groupName'], $payload['groupName']);
        self::assertEquals($group->getGroupID(), $payload['groupID']);

        $updatedGroup = $this->groupNameRepository->findOneBy(['groupID' => $group->getGroupID()]);

        self::assertEquals($formRequestData['groupName'], $updatedGroup->getGroupName());
    }

    public function test_updating_group_name_too_long(): void
    {
        /** @var GroupNames[] $groupsUserNotApartOf */
        $groupsUserNotApartOf = $this->groupNameRepository->findGroupsUserIsNotApartOf($this->user);

        if (empty($groupsUserNotApartOf)) {
            self::fail('No groups found for user to update');
        }

        $group = $groupsUserNotApartOf[0];

        $formRequestData = [
            'groupName' => 'NewGroupNameNewGroupNameNewGroupNameNewGroupNamewww',
        ];

        $jsonData = json_encode($formRequestData);

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_GROUP_URL, $group->getGroupID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
            $jsonData
        );

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $response = json_decode($this->client->getResponse()->getContent(), true);

        self::assertEquals(UpdateGroupController::BAD_REQUEST_NO_DATA_RETURNED, $response['title']);
        self::assertEquals(["Group name cannot be longer than 50 characters"], $response['errors']);
    }

    public function test_updating_group_name_too_short(): void
    {
        /** @var GroupNames[] $groupsUserNotApartOf */
        $groupsUserNotApartOf = $this->groupNameRepository->findGroupsUserIsNotApartOf($this->user);

        if (empty($groupsUserNotApartOf)) {
            self::fail('No groups found for user to update');
        }

        $group = $groupsUserNotApartOf[0];

        $formRequestData = [
            'groupName' => 'N',
        ];

        $jsonData = json_encode($formRequestData);

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_GROUP_URL, $group->getGroupID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
            $jsonData
        );

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $response = json_decode($this->client->getResponse()->getContent(), true);

        self::assertEquals(UpdateGroupController::BAD_REQUEST_NO_DATA_RETURNED, $response['title']);
        self::assertEquals(["Group name must be at least 2 characters long"], $response['errors']);
    }

    public function test_updating_group_name_to_already_existing_group_name(): void
    {
        /** @var GroupNames[] $groups */
        $groups = $this->groupNameRepository->findAll();

        if (empty($groups)) {
            self::fail('No groups found for user to update');
        }

        $groupToUpdate = $groups[0];
        $groupNameToTryUpdateTo = $groups[1]->getGroupName();

        $formRequestData = [
            'groupName' => $groupNameToTryUpdateTo,
        ];

        $jsonData = json_encode($formRequestData);

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_GROUP_URL, $groupToUpdate->getGroupID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
            $jsonData
        );

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $response = json_decode($this->client->getResponse()->getContent(), true);

        self::assertEquals(UpdateGroupController::BAD_REQUEST_NO_DATA_RETURNED, $response['title']);
        self::assertEquals(["Group name already exists"], $response['errors']);
    }

    public function test_updating_group_name_correct_data_admin_user(): void
    {
        /** @var GroupNames[] $groups */
        $groups = $this->groupNameRepository->findAll();

        if (empty($groups)) {
            self::fail('No groups found for user to update');
        }

        $group = $groups[0];

        $formRequestData = [
            'groupName' => 'NewGroupName',
        ];

        $jsonData = json_encode($formRequestData);

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_GROUP_URL, $group->getGroupID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
            $jsonData
        );

        self::assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);

        $response = json_decode($this->client->getResponse()->getContent(), true);

        $payload = $response['payload'];

        self::assertEquals($formRequestData['groupName'], $payload['groupName']);
        self::assertEquals($group->getGroupID(), $payload['groupID']);

        $updatedGroup = $this->groupNameRepository->findOneBy(['groupID' => $group->getGroupID()]);

        self::assertEquals($formRequestData['groupName'], $updatedGroup->getGroupName());
    }

    public function test_updating_group_name_correct_data_regular_user(): void
    {
        /** @var GroupNames[] $groupsUserNotApartOf */
        $groupsUserNotApartOf = $this->groupNameRepository->findGroupsUserIsApartOf($this->regularUserTwo);

        if (empty($groupsUserNotApartOf)) {
            self::fail('No groups found for user to update');
        }

        $group = $groupsUserNotApartOf[0];

        $formRequestData = [
            'groupName' => 'NewGroupName',
        ];

        $jsonData = json_encode($formRequestData);

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_GROUP_URL, $group->getGroupID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
            $jsonData
        );

        self::assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);

        $response = json_decode($this->client->getResponse()->getContent(), true);

        $payload = $response['payload'];

        self::assertEquals($formRequestData['groupName'], $payload['groupName']);
        self::assertEquals($group->getGroupID(), $payload['groupID']);

        $updatedGroup = $this->groupNameRepository->findOneBy(['groupID' => $group->getGroupID()]);

        self::assertEquals($formRequestData['groupName'], $updatedGroup->getGroupName());
    }

    public function test_updating_group_check_full_response(): void
    {
        /** @var GroupNames[] $groups */
        $groups = $this->groupNameRepository->findAll();

        if (empty($groups)) {
            self::fail('No groups found for user to update');
        }

        $group = $groups[0];

        $formRequestData = [
            'groupName' => 'NewGroupName',
        ];

        $jsonData = json_encode($formRequestData);

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_GROUP_URL, $group->getGroupID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
            $jsonData
        );

        self::assertResponseStatusCodeSame(Response::HTTP_ACCEPTED);

        $response = json_decode($this->client->getResponse()->getContent(), true);

        $payload = $response['payload'];

        self::assertEquals($formRequestData['groupName'], $payload['groupName']);
        self::assertEquals($group->getGroupID(), $payload['groupID']);
    }
}
