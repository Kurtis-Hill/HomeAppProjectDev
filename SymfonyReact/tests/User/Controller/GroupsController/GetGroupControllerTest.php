<?php

namespace App\Tests\User\Controller\GroupsController;

use App\ORM\DataFixtures\Core\UserDataFixtures;
use App\Authentication\Controller\SecurityController;
use App\Tests\Traits\TestLoginTrait;
use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetGroupControllerTest extends WebTestCase
{
    use TestLoginTrait;

    private const GET_USER_GROUPS_URL = '/HomeApp/api/user/user-groups/all';

    private ?EntityManagerInterface $entityManager;

    private KernelBrowser $client;

    private string $userToken;

    private User $user;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::ADMIN_USER_EMAIL_ONE]);
        $this->userToken = $this->setUserToken($this->client);
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
        parent::tearDown();
    }

    public function test_user_groups_are_correct_admin(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            self::GET_USER_GROUPS_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer ' . $this->userToken]
        );

        $requestResponse = $this->client->getResponse();
        $responseData = json_decode($requestResponse->getContent(), true);

        $payload = $responseData['payload'];

        self::assertCount(1, $payload);
        self::assertEquals(UserDataFixtures::ADMIN_GROUP_ONE, $payload[0]['groupName']);
        self::assertIsNumeric($payload[0]['groupID']);
        self::assertEquals(Response::HTTP_OK, $requestResponse->getStatusCode());
    }

    public function test_user_groups_are_correct_regular_user_admin_group(): void
    {
        $userToken = $this->setUserToken(
            $this->client,
            UserDataFixtures::REGULAR_USER_EMAIL_TWO,
            UserDataFixtures::REGULAR_PASSWORD
        );
        $this->client->request(
            Request::METHOD_GET,
            self::GET_USER_GROUPS_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer ' . $userToken]
        );

        $requestResponse = $this->client->getResponse();
        $responseData = json_decode($requestResponse->getContent(), true);

        foreach ($responseData['payload'] as $key => $payload) {
            self::assertEquals(UserDataFixtures::GROUPS_SECOND_REGULAR_USER_IS_ADDED_TO[$key], $payload['groupName']);
            self::assertIsNumeric($payload['groupID']);
        }
        self::assertEquals(Response::HTTP_OK, $requestResponse->getStatusCode());
        self::assertCount(count(UserDataFixtures::GROUPS_SECOND_REGULAR_USER_IS_ADDED_TO), $responseData['payload']);
    }

    public function test_user_groups_are_correct_regular_user_none_other_groups(): void
    {
        $userToken = $this->setUserToken(
            $this->client,
            UserDataFixtures::REGULAR_USER_EMAIL_ONE,
            UserDataFixtures::REGULAR_PASSWORD
        );
        $this->client->request(
            Request::METHOD_GET,
            self::GET_USER_GROUPS_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer ' . $userToken]
        );

        $requestResponse = $this->client->getResponse();
        $responseData = json_decode($requestResponse->getContent(), true);

        $payload = $responseData['payload'];
        self::assertEquals(UserDataFixtures::REGULAR_GROUP_ONE, $payload[0]['groupName']);
        self::assertIsNumeric($payload[0]['groupID']);

        self::assertEquals(Response::HTTP_OK, $requestResponse->getStatusCode());
        self::assertCount(2, $responseData);
    }

    /**
     * @dataProvider wrongHttpsMethodDataProvider
     */
    public function test_using_wrong_http_method(string $httpVerb): void
    {
        $this->client->request(
            $httpVerb,
            self::GET_USER_GROUPS_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );

        self::assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $this->client->getResponse()->getStatusCode());
    }

    public function wrongHttpsMethodDataProvider(): array
    {
        return [
            [Request::METHOD_PUT],
            [Request::METHOD_PATCH],
            [Request::METHOD_DELETE],
        ];
    }
}
