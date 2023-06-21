<?php

namespace App\Tests\User\Controller\UserControllers;

use App\Common\API\CommonURL;
use App\ORM\DataFixtures\Core\UserDataFixtures;
use App\Tests\Traits\TestLoginTrait;
use App\User\Entity\User;
use App\User\Repository\ORM\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DeleteUserControllerTest extends WebTestCase
{
    use TestLoginTrait;

    private const DELETE_USER_URL = CommonURL::USER_HOMEAPP_API_URL . '%d/delete';

    private ?EntityManagerInterface $entityManager;

    private KernelBrowser $client;

    private User $regularUserTwo;

    private UserRepository $userRepository;

    private ?string $userToken;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->userRepository = $this->entityManager->getRepository(User::class);
        $this->regularUserTwo = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_TWO]);
        $this->userToken = $this->setUserToken($this->client);
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
    public function test_adding_device_wrong_http_method(string $httpVerb): void
    {
        $this->client->request(
            $httpVerb,
            self::DELETE_USER_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );

        self::assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $this->client->getResponse()->getStatusCode());
    }

    public function wrongHttpsMethodDataProvider(): array
    {
        return [
            [Request::METHOD_GET],
            [Request::METHOD_PUT],
            [Request::METHOD_PATCH],
            [Request::METHOD_POST],
        ];
    }

    public function test_deleting_user_does_not_exist(): void
    {
        while (true) {
            $randomId = random_int(1, 999);
            if (!$this->userRepository->find($randomId)) {
                break;
            }
        }

        $this->client->request(
            Request::METHOD_DELETE,
            sprintf(self::DELETE_USER_URL, $randomId),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken]
        );
        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
    }

    public function test_deleting_user_regular_user(): void
    {
        $allUsers = $this->userRepository->findAll();

        $user = $allUsers[array_rand($allUsers)];

        $userToken = $this->setUserToken(
            $this->client,
            $this->regularUserTwo->getEmail(),
            UserDataFixtures::REGULAR_PASSWORD
        );
        $this->client->request(
            Request::METHOD_DELETE,
            sprintf(self::DELETE_USER_URL, $user->getUserID()),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $userToken]
        );
        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        $deletedUser = $this->userRepository->find($user->getUserID());
        self::assertNotNull($deletedUser);
    }

    public function test_deleting_user_admin_user(): void
    {
        $allUsers = $this->userRepository->findAll();

        $user = $allUsers[array_rand($allUsers)];

        $this->client->request(
            Request::METHOD_DELETE,
            sprintf(self::DELETE_USER_URL, $user->getUserID()),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken]
        );
        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $payload = $responseData['payload'];
        self::assertEquals('User removed with ID: ' . $user->getUserID(), $payload[0]);

        $deletedUser = $this->userRepository->find($user->getUserID());
        self::assertNull($deletedUser);
    }
}
