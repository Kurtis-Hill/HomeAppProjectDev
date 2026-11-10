<?php

namespace App\Tests\Controller\User\UserControllers;

use App\DataFixtures\Core\UserDataFixtures;
use App\Services\API\CommonURL;
use App\Tests\Traits\TestLoginTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetAllUsersControllerTest extends WebTestCase
{
    use TestLoginTrait;

    private const GET_ALL_USERS_URL = CommonURL::USER_HOMEAPP_API_URL;

    private ?EntityManagerInterface $entityManager;

    private KernelBrowser $client;

    private ?string $adminUserToken;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->adminUserToken = $this->setUserToken($this->client);
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;

        parent::tearDown();
    }

    public function test_regular_user_cannot_get_all_users(): void
    {
        $regularUserToken = $this->setUserToken(
            $this->client,
            UserDataFixtures::REGULAR_USER_EMAIL_ONE,
            UserDataFixtures::REGULAR_PASSWORD
        );

        $this->client->request(
            Request::METHOD_GET,
            self::GET_ALL_USERS_URL,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $regularUserToken],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function test_admin_user_can_get_all_users_returns_ok(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            self::GET_ALL_USERS_URL,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->adminUserToken],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function test_admin_user_get_all_users_returns_correct_structure(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            self::GET_ALL_USERS_URL,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->adminUserToken],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        self::assertArrayHasKey('title', $responseData);
        self::assertArrayHasKey('payload', $responseData);
        self::assertEquals('Request Successful', $responseData['title']);
        self::assertIsArray($responseData['payload']);
        self::assertNotEmpty($responseData['payload']);
    }

    public function test_admin_user_get_all_users_returns_all_fixture_users(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            self::GET_ALL_USERS_URL,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->adminUserToken],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $payload = $responseData['payload'];

        $expectedEmails = [
            UserDataFixtures::ADMIN_USER_EMAIL_ONE,
            UserDataFixtures::ADMIN_USER_EMAIL_TWO,
            UserDataFixtures::REGULAR_USER_EMAIL_ONE,
            UserDataFixtures::REGULAR_USER_EMAIL_TWO,
            UserDataFixtures::REGULAR_USER_EMAIL_THREE,
        ];

        $returnedEmails = array_column($payload, 'email');

        foreach ($expectedEmails as $email) {
            self::assertContains($email, $returnedEmails, "Expected email $email not found in response.");
        }
    }

    public function test_admin_user_get_all_users_payload_has_expected_keys(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            self::GET_ALL_USERS_URL,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->adminUserToken],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $firstUser = $responseData['payload'][0];

        self::assertArrayHasKey('userID', $firstUser);
        self::assertArrayHasKey('firstName', $firstUser);
        self::assertArrayHasKey('lastName', $firstUser);
        self::assertArrayHasKey('email', $firstUser);
        self::assertArrayHasKey('roles', $firstUser);
    }

    public function test_request_without_token_returns_unauthorized(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            self::GET_ALL_USERS_URL,
        );

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }
}
