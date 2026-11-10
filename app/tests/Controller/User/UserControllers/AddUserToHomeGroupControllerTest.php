<?php

namespace App\Tests\Controller\User\UserControllers;

use App\DataFixtures\Core\UserDataFixtures;
use App\Entity\Authentication\GroupMapping;
use App\Entity\User\Group;
use App\Entity\User\User;
use App\Repository\Authentication\ORM\GroupMappingRepository;
use App\Repository\User\ORM\UserRepository;
use App\Services\API\APIErrorMessages;
use App\Controller\User\UserControllers\AddUserToHomeGroupController;
use App\Services\API\CommonURL;
use App\Tests\Traits\TestLoginTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AddUserToHomeGroupControllerTest extends WebTestCase
{
    use TestLoginTrait;

    private const ADD_USER_TO_HOME_GROUP_URL = CommonURL::USER_HOMEAPP_API_URL . '%d/home-group';

    private ?EntityManagerInterface $entityManager;

    private KernelBrowser $client;

    private UserRepository $userRepository;

    private GroupMappingRepository $groupMappingRepository;

    private User $adminUserOne;

    private User $adminUserTwo;

    private User $regularUserOne;

    private User $regularUserTwo;

    private ?string $adminUserToken;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->adminUserOne = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::ADMIN_USER_EMAIL_ONE]);
        $this->adminUserTwo = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::ADMIN_USER_EMAIL_TWO]);
        $this->regularUserOne = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_ONE]);
        $this->regularUserTwo = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_TWO]);
        $this->userRepository = $this->entityManager->getRepository(User::class);
        $this->groupMappingRepository = $this->entityManager->getRepository(GroupMapping::class);
        $this->adminUserToken = $this->setUserToken($this->client);
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;

        parent::tearDown();
    }

    public function test_regular_user_cannot_add_user_to_home_group(): void
    {
        $regularUserToken = $this->setUserToken(
            $this->client,
            UserDataFixtures::REGULAR_USER_EMAIL_TWO,
            UserDataFixtures::REGULAR_PASSWORD
        );

        $this->client->request(
            Request::METHOD_POST,
            sprintf(self::ADD_USER_TO_HOME_GROUP_URL, $this->adminUserTwo->getUserID()),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $regularUserToken],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function test_non_existent_user_id_returns_not_found(): void
    {
        while (true) {
            $randomId = random_int(1, 999999);
            if ($this->userRepository->find($randomId) === null) {
                break;
            }
        }

        $this->client->request(
            Request::METHOD_POST,
            sprintf(self::ADD_USER_TO_HOME_GROUP_URL, $randomId),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->adminUserToken],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_user_already_in_home_group_returns_bad_request(): void
    {
        // regularUserOne is already mapped to the home group in fixtures
        $this->client->request(
            Request::METHOD_POST,
            sprintf(self::ADD_USER_TO_HOME_GROUP_URL, $this->regularUserOne->getUserID()),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->adminUserToken],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        self::assertArrayHasKey('errors', $responseData);
        self::assertEquals([GroupMapping::GROUP_NAME_MAPPING_EXISTS], $responseData['errors']);
        self::assertEquals(AddUserToHomeGroupController::BAD_REQUEST_NO_DATA_RETURNED, $responseData['title']);
    }

    public function test_admin_successfully_adds_user_to_home_group(): void
    {
        // adminUserTwo is NOT in the home group in fixtures
        $this->client->request(
            Request::METHOD_POST,
            sprintf(self::ADD_USER_TO_HOME_GROUP_URL, $this->adminUserTwo->getUserID()),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->adminUserToken],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        self::assertEquals(AddUserToHomeGroupController::REQUEST_SUCCESSFUL, $responseData['title']);
        self::assertContains(
            'User added to ' . Group::HOME_APP_GROUP_NAME . ' successfully',
            $responseData['payload']
        );

        $this->entityManager->clear();
        $homeGroup = $this->entityManager->getRepository(Group::class)->findOneBy(['groupName' => Group::HOME_APP_GROUP_NAME]);
        $mapping = $this->groupMappingRepository->findOneBy([
            'user' => $this->adminUserTwo->getUserID(),
            'groupID' => $homeGroup->getGroupID(),
        ]);
        self::assertNotNull($mapping);
    }
}
