<?php

namespace App\Tests;

use App\Authentication\Controller\SecurityController;
use App\DataFixtures\Core\UserDataFixtures;
use App\DataFixtures\ESP8266\ESP8266DeviceFixtures;
use App\Devices\Entity\Devices;
use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class AuthenticationTests extends WebTestCase
{
    private const REFRESH_TOKEN_URL = '/HomeApp/api/user/token/refresh';

    private KernelBrowser $client;

    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    /**
     * @dataProvider userCredentialsDataProvider
     */
    public function test_can_get_user_token(string $username, string $password, array $role): void
    {
        $userRepository = $this->entityManager->getRepository(User::class);
        $testUser = $userRepository->findOneBy(['email' => $username]);

        $this->client->request(
            Request::METHOD_POST,
            SecurityController::API_USER_LOGIN,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"username":"' . $username . '","password":"' . $password .'"}'
        );

        $requestResponse = $this->client->getResponse();
        $responseData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertArrayHasKey('token', $responseData);
        self::assertArrayHasKey('refreshToken', $responseData);
        self::assertArrayHasKey('userData', $responseData);
        self::assertNotNull($responseData['token']);
        self::assertNotNull($responseData['refreshToken']);
        self::assertNotNull($responseData['userData']);

        self::assertEquals($testUser->getUserID(), $responseData['userData']['userID']);
        self::assertEquals($role, $responseData['userData']['roles']);
        self::assertEquals(200, $requestResponse->getStatusCode());
    }

    /**
     * @dataProvider deviceCredentialsDataProvider
     */
    public function test_can_get_device_token(string $username, string $password, string $ipAddress, string $externalIpAddress): void
    {
        $device = $this->entityManager->getRepository(Devices::class)->findOneBy(['deviceName' => $username]);
        $this->client->request(
            Request::METHOD_POST,
            SecurityController::API_DEVICE_LOGIN,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"username":"' . $username . '","password":"' . $password .'","ipAddress":"' . $ipAddress .'","externalIpAddress":"' . $externalIpAddress .'"}'
        );

        $requestResponse = $this->client->getResponse();

        $responseData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertArrayHasKey('token', $responseData);
        self::assertArrayHasKey('refreshToken', $responseData);
        self::assertNotNull($responseData['token']);
        self::assertNotNull($responseData['refreshToken']);

        self::assertEquals($device->getIpAddress(), $ipAddress);
        self::assertEquals($device->getExternalIpAddress(), $externalIpAddress);

        self::assertEquals(200, $requestResponse->getStatusCode());
    }

    /**
     * @dataProvider userCredentialsDataProvider
     */
    public function test_get_user_refresh_token(string $username, string $password): void
    {
        $this->client->request(
            Request::METHOD_POST,
            SecurityController::API_USER_LOGIN,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"username":"' . $username . '","password":"' . $password .'"}'
        );

        $requestResponse = $this->client->getResponse();
        $responseData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $refreshToken = $responseData['refreshToken'];

        $this->client->request(
            Request::METHOD_POST,
            self::REFRESH_TOKEN_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"refreshToken":"' . $refreshToken . '"}'
        );

        $refreshTokenResponseData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertArrayHasKey('token', $responseData);
        self::assertArrayHasKey('refreshToken', $responseData);

        self::assertNotNull($refreshTokenResponseData['token']);
        self::assertNotNull($refreshTokenResponseData['refreshToken']);

        self::assertEquals(200, $requestResponse->getStatusCode());
    }

    /**
     * @dataProvider deviceCredentialsDataProvider
     */
    public function test_get_device_refresh_token(string $username, string $password): void
    {
        $this->client->request(
            Request::METHOD_POST,
            SecurityController::API_DEVICE_LOGIN,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"username":"' . $username . '","password":"' . $password .'"}'
        );

        $requestResponse = $this->client->getResponse();
        $responseData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $refreshToken = $responseData['refreshToken'];

        $this->client->request(
            Request::METHOD_POST,
            self::REFRESH_TOKEN_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"refreshToken":"' . $refreshToken . '"}'
        );

        $refreshTokenResponseData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertArrayHasKey('token', $responseData);
        self::assertArrayHasKey('refreshToken', $responseData);

        self::assertNotNull($refreshTokenResponseData['token']);
        self::assertNotNull($refreshTokenResponseData['refreshToken']);

        self::assertEquals(200, $requestResponse->getStatusCode());
    }

    public function deviceCredentialsDataProvider(): Generator
    {
        yield [
            'username' => ESP8266DeviceFixtures::LOGIN_TEST_ACCOUNT_NAME['name'],
            'password' => ESP8266DeviceFixtures::LOGIN_TEST_ACCOUNT_NAME['password'],
            'ipAddress' => "192.168.1.43",
            'externalIpAddress' => "86.24.1.113",
        ];
    }

    public function userCredentialsDataProvider(): Generator
    {
        yield [
            'username' => UserDataFixtures::ADMIN_USER,
            'password' => UserDataFixtures::ADMIN_PASSWORD,
            'roles' => ['ROLE_ADMIN'],
        ];

        yield [
            'username' => UserDataFixtures::REGULAR_USER,
            'password' => UserDataFixtures::REGULAR_PASSWORD,
            'roles' => ['ROLE_USER'],
        ];
    }
}
