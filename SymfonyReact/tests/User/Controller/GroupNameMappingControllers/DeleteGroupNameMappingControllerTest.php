<?php

namespace App\Tests\User\Controller\GroupNameMappingControllers;

use App\Authentication\Entity\GroupNameMapping;
use App\Authentication\Repository\ORM\GroupNameMappingRepository;
use App\Common\API\CommonURL;
use App\ORM\DataFixtures\Core\UserDataFixtures;
use App\Tests\Traits\TestLoginTrait;
use App\User\Controller\GroupNameMappingControllers\DeleteGroupNameMappingController;
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

class DeleteGroupNameMappingControllerTest extends WebTestCase
{
    use TestLoginTrait;

    private const DELETE_GROUP_NAME_MAPPING_URL = CommonURL::USER_HOMEAPP_API_URL . 'group-mapping/' . '%d/delete';

    private ?EntityManagerInterface $entityManager;

    private KernelBrowser $client;

    private string $userToken;

    private User $adminUser;

    private User $regularUserTwo;

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

    /**
     * @dataProvider wrongHttpsMethodDataProvider
     */
    public function test_using_wrong_http_method(string $httpVerb): void
    {
        /** @var GroupNameMapping[] $groupNameMappings */
        $groupNameMappings = $this->groupNameMappingRepository->findAll();

        $this->client->request(
            $httpVerb,
            sprintf(self::DELETE_GROUP_NAME_MAPPING_URL, $groupNameMappings[0]->getGroupNameMappingID()),
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
        yield [Request::METHOD_POST];
    }

    public function test_regular_user_cannot_delete_mapping_for_group_doesnt_own(): void
    {
        /** @var GroupNames[] $groupsRegularUserTwoDoesntOwn */
        $groupsRegularUserTwoDoesntOwn = $this->groupNameRepository->findGroupsUserIsNotApartOf($this->adminUser);

        $groupNameMappingsRegularUserTwoDoesntOwn = $this->groupNameMappingRepository->findBy(['groupName' => $groupsRegularUserTwoDoesntOwn]);

        self::assertNotEmpty($groupNameMappingsRegularUserTwoDoesntOwn);

        $userToken = $this->setUserToken($this->client, $this->regularUserTwo->getEmail(), UserDataFixtures::REGULAR_PASSWORD);

        $this->client->request(
            Request::METHOD_DELETE,
            sprintf(self::DELETE_GROUP_NAME_MAPPING_URL, $groupNameMappingsRegularUserTwoDoesntOwn[0]->getGroupNameMappingID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $userToken],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $title = $responseData['title'];

        self::assertEquals(DeleteGroupNameMappingController::NOT_AUTHORIZED_TO_BE_HERE, $title);
    }

    public function test_regular_user_can_delete_mapping_for_group_owns(): void
    {
        /** @var GroupNameMapping[] $groupNameMappingsRegularUserTwoOwns */
        $groupNameMappingsRegularUserTwoOwns = $this->groupNameMappingRepository->findBy(['groupName' => $this->regularUserTwo->getGroupNameID()->getGroupNameID()]);

        self::assertNotEmpty($groupNameMappingsRegularUserTwoOwns);
        $userToken = $this->setUserToken($this->client, $this->regularUserTwo->getEmail(), UserDataFixtures::REGULAR_PASSWORD);

        $groupNameMappingToDelete = $groupNameMappingsRegularUserTwoOwns[0];

        $groupNameMappingID = $groupNameMappingToDelete->getGroupNameMappingID();
        $this->client->request(
            Request::METHOD_DELETE,
            sprintf(self::DELETE_GROUP_NAME_MAPPING_URL, $groupNameMappingID),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $userToken],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $title = $responseData['title'];
        self::assertEquals(DeleteGroupNameMappingController::REQUEST_SUCCESSFUL, $title);

        $payload = $responseData['payload'];
        self::assertEquals([
            sprintf(
            DeleteGroupNameMappingController::DELETE_GROUP_NAME_MAPPING_SUCCESS,
            $groupNameMappingID
            )
        ], $payload);
    }

    public function test_admin_can_delete_group_mapping_not_owned(): void
    {
        /** @var GroupNames[] $groupsAdminNotApartOf */
        $groupsAdminNotApartOf = $this->groupNameRepository->findGroupsUserIsNotApartOf($this->adminUser);

        $groupNameMapping = $this->groupNameMappingRepository->findBy(['groupName' => $groupsAdminNotApartOf[0]->getGroupNameID()]);
        $this->client->request(
            Request::METHOD_DELETE,
            sprintf(self::DELETE_GROUP_NAME_MAPPING_URL, $groupNameMapping[0]->getGroupNameMappingID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $title = $responseData['title'];
        self::assertEquals(DeleteGroupNameMappingController::REQUEST_SUCCESSFUL, $title);

        $payload = $responseData['payload'];
        self::assertEquals([
            sprintf(
            DeleteGroupNameMappingController::DELETE_GROUP_NAME_MAPPING_SUCCESS,
            $groupNameMapping[0]->getGroupNameMappingID()
            )
        ], $payload);
    }
}
