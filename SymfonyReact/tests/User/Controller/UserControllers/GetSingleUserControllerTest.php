<?php

namespace App\Tests\User\Controller\UserControllers;

use App\Common\API\CommonURL;
use App\ORM\DataFixtures\Core\UserDataFixtures;
use App\Tests\Traits\TestLoginTrait;
use App\User\Entity\Group;
use App\User\Entity\User;
use App\User\Repository\ORM\GroupRepositoryInterface;
use App\User\Repository\ORM\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetSingleUserControllerTest extends WebTestCase
{
    use TestLoginTrait;

    private const GET_USER_URL = CommonURL::USER_HOMEAPP_API_URL . '%d/get';

    private ?EntityManagerInterface $entityManager;

    private KernelBrowser $client;

    private User $regularUserTwo;

    private UserRepository $userRepository;

    private GroupRepositoryInterface $groupRepository;

    private ?string $adminUserToken;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->regularUserTwo = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_TWO]);
        $this->adminUserToken = $this->setUserToken($this->client);
        $this->userRepository = $this->entityManager->getRepository(User::class);
        $this->groupRepository = $this->entityManager->getRepository(Group::class);
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;

        parent::tearDown();
    }

    public function test_user_cannot_get_another_user(): void
    {
        $regularUserToken = $this->setUserToken(
            $this->client,
            UserDataFixtures::REGULAR_USER_EMAIL_TWO,
            UserDataFixtures::REGULAR_PASSWORD
        );

        $anyUser = null;

        while ($anyUser === null) {
            /** @var User[] $users */
            $users = $this->userRepository->findAll();
            foreach ($users as $user) {
                if ($user->getUserID() !== $this->regularUserTwo->getUserID()) {
                    $anyUser = $user;
                    break;
                }
            }
        }
        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::GET_USER_URL, $anyUser->getUserID()),
            server: ['HTTP_AUTHORIZATION' => sprintf('Bearer %s', $regularUserToken)]
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function test_admin_can_get_any_user(): void
    {
        $anyUser = null;
        while ($anyUser === null) {
            /** @var User[] $users */
            $users = $this->userRepository->findAll();
            foreach ($users as $user) {
                if ($user->getUserID() !== $this->regularUserTwo->getUserID()) {
                    $anyUser = $user;
                    break;
                }
            }
        }
        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::GET_USER_URL, $anyUser->getUserID()),
            server: ['HTTP_AUTHORIZATION' => sprintf('Bearer %s', $this->adminUserToken)]
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function test_user_can_get_himself(): void
    {
        $regularUserToken = $this->setUserToken(
            $this->client,
            UserDataFixtures::REGULAR_USER_EMAIL_TWO,
            UserDataFixtures::REGULAR_PASSWORD
        );

        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::GET_USER_URL, $this->regularUserTwo->getUserID()),
            server: ['HTTP_AUTHORIZATION' => sprintf('Bearer %s', $regularUserToken)]
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function test_full_user_data_response(): void
    {
        $regularUserToken = $this->setUserToken(
            $this->client,
            UserDataFixtures::REGULAR_USER_EMAIL_TWO,
            UserDataFixtures::REGULAR_PASSWORD
        );

        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::GET_USER_URL, $this->regularUserTwo->getUserID()),
            server: ['HTTP_AUTHORIZATION' => sprintf('Bearer %s', $regularUserToken)]
        );

        $response = $this->client->getResponse();
        $responseData = json_decode($response->getContent(), true);

        $userData = $responseData['payload'];

        self::assertEquals($this->regularUserTwo->getUserID(), $userData['userID']);
        self::assertEquals($this->regularUserTwo->getEmail(), $userData['email']);
        self::assertEquals($this->regularUserTwo->getFirstName(), $userData['firstName']);
        self::assertEquals($this->regularUserTwo->getLastName(), $userData['lastName']);
        self::assertEquals($this->regularUserTwo->getGroup()->getGroupID(), $userData['group']['groupID']);
        self::assertEquals($this->regularUserTwo->getGroup()->getGroupName(), $userData['group']['groupName']);
        self::assertTrue($userData['canEdit']);
        self::assertFalse($userData['canDelete']);
    }
}
