<?php

namespace App\Tests\Authentication\EventListeners;

use App\DataFixtures\Core\UserDataFixtures;
use App\DataFixtures\ESP8266\ESP8266DeviceFixtures;
use App\Entity\Device\Devices;
use App\Entity\User\User;
use App\Repository\User\ORM\UserRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class AuthenticationTest extends WebTestCase
{
    private const API_USER_REFRESH_TOKEN_URL = '/HomeApp/api/user/token/refresh';

    private const API_DEVICE_REFRESH_TOKEN_URL = '/HomeApp/api/device/token/refresh';

    public const API_USER_LOGIN = '/HomeApp/api/user/login_check';

    public const API_DEVICE_LOGIN = '/HomeApp/api/device/login_check';

    private KernelBrowser $client;

    private ?EntityManagerInterface $entityManager;

    private UserRepositoryInterface $userRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->userRepository = $this->entityManager->getRepository(User::class);
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
        parent::tearDown();
    }

    /**
     * @dataProvider userCredentialsDataProvider
     */
    public function test_can_get_user_token(string $username, string $password, array $role): void
    {
        /** @var User $testUser */
        $testUser = $this->userRepository->findOneBy(['email' => $username]);

        $this->client->request(
            Request::METHOD_POST,
            self::API_USER_LOGIN,
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
        self::assertNotNull($responseData['userData']['userID']);
        self::assertNotNull($responseData['userData']['roles']);

        self::assertEquals($testUser->getUserID(), $responseData['userData']['userID']);
        self::assertEquals($role, $responseData['userData']['roles']);
        self::assertEquals(200, $requestResponse->getStatusCode());
    }

    /**
     * @dataProvider deviceCredentialsDataProvider
     */
    public function test_can_get_device_token(string $username, string $password, string $ipAddress, string $externalIpAddress): void
    {
        /** @var \App\Entity\Device\Devices $device */
        $device = $this->entityManager->getRepository(Devices::class)->findOneBy(['deviceName' => $username]);
        $this->client->request(
            Request::METHOD_POST,
            self::API_DEVICE_LOGIN,
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
            self::API_USER_LOGIN,
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
            self::API_USER_REFRESH_TOKEN_URL,
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
            self::API_DEVICE_LOGIN,
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
            self::API_DEVICE_REFRESH_TOKEN_URL,
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
            'username' => ESP8266DeviceFixtures::LOGIN_TEST_ACCOUNT_NAME_ADMIN_GROUP_ONE['name'],
            'password' => ESP8266DeviceFixtures::LOGIN_TEST_ACCOUNT_NAME_ADMIN_GROUP_ONE['password'],
            'ipAddress' => "192.168.1.43",
            'externalIpAddress' => "86.24.1.113",
        ];
    }

    public function userCredentialsDataProvider(): Generator
    {
        yield [
            'username' => UserDataFixtures::ADMIN_USER_EMAIL_ONE,
            'password' => UserDataFixtures::ADMIN_PASSWORD,
            'roles' => ['ROLE_ADMIN'],
        ];

        yield [
            'username' => UserDataFixtures::ADMIN_USER_EMAIL_TWO,
            'password' => UserDataFixtures::ADMIN_PASSWORD,
            'roles' => ['ROLE_ADMIN'],
        ];

        yield [
            'username' => UserDataFixtures::REGULAR_USER_EMAIL_ONE,
            'password' => UserDataFixtures::REGULAR_PASSWORD,
            'roles' => ['ROLE_USER'],
        ];

        yield [
            'username' => UserDataFixtures::REGULAR_USER_EMAIL_TWO,
            'password' => UserDataFixtures::REGULAR_PASSWORD,
            'roles' => ['ROLE_USER'],
        ];
    }

    /**
     * @dataProvider userWrongCredentialsDataProvider
     */
    public function test_user_login_wrong_credentials(string $username, string $password): void
    {
        $this->client->request(
            Request::METHOD_POST,
            self::API_USER_LOGIN,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"username":"' . $username . '","password":"' . $password .'"}'
        );

        $requestResponse = $this->client->getResponse();
        $responseData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);


        self::assertArrayNotHasKey('token', $responseData);
        self::assertArrayNotHasKey('refreshToken', $responseData);

        self::assertEquals(401, $requestResponse->getStatusCode());
    }

    public function userWrongCredentialsDataProvider(): Generator
    {
        yield [
            'username' => UserDataFixtures::ADMIN_USER_EMAIL_TWO,
            'password' => 'wrong_password',
        ];

        yield [
            'username' => 'Wrong_username',
            'password' => UserDataFixtures::ADMIN_PASSWORD,
        ];

        yield [
            'username' => 'Wrong_username',
            'password' => 'Wrong_password',
        ];
    }

    /**
     * @dataProvider deviceWrongCredentialsDataProvider
     */
    public function test_device_login_wrong_credentials(string $username, string $password): void
    {
        $this->client->request(
            Request::METHOD_POST,
            self::API_DEVICE_LOGIN,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"username":"' . $username . '","password":"' . $password .'"}'
        );

        $requestResponse = $this->client->getResponse();
        $responseData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);


        self::assertArrayNotHasKey('token', $responseData);
        self::assertArrayNotHasKey('refreshToken', $responseData);

        self::assertEquals(401, $requestResponse->getStatusCode());
    }

    public function deviceWrongCredentialsDataProvider(): Generator
    {
        yield [
            'username' => ESP8266DeviceFixtures::LOGIN_TEST_ACCOUNT_NAME_ADMIN_GROUP_ONE['name'],
            'password' => 'wrong_password',
        ];

        yield [
            'username' => 'Wrong_username',
            'password' => ESP8266DeviceFixtures::LOGIN_TEST_ACCOUNT_NAME_ADMIN_GROUP_ONE['password'],
        ];

        yield [
            'username' => 'Wrong_username',
            'password' => 'Wrong_password',
        ];
    }

//    public function test_user_login_throttling(): void
//    {
//        for ($i = 0; $i < 5; $i++) {
//            $this->client->request(
//                Request::METHOD_POST,
//                self::API_USER_LOGIN,
//                [],
//                [],
//                ['CONTENT_TYPE' => 'application/json'],
//                '{"username":"' . UserDataFixtures::ADMIN_USER . '","password":"' . UserDataFixtures::ADMIN_PASSWORD .'1"}'
//            );
//
//            self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
//        }
//
//        $this->client->request(
//            Request::METHOD_POST,
//            self::API_USER_LOGIN,
//            [],
//            [],
//            ['CONTENT_TYPE' => 'application/json'],
//            '{"username":"' . UserDataFixtures::ADMIN_USER . '","password":"' . UserDataFixtures::ADMIN_PASSWORD .'"}'
//        );
//        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
//
//        $requestResponse = $this->client->getResponse();
//        $responseData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);
//
//        self::assertEquals('Too many failed login attempts, please try again in 1 minute.', $responseData['message']);
//    }

//    public function test_device_login_throttling(): void
//    {
//        for ($i = 0; $i < 5; $i++) {
//            $this->client->request(
//                Request::METHOD_POST,
//                self::API_DEVICE_LOGIN,
//                [],
//                [],
//                ['CONTENT_TYPE' => 'application/json'],
//                '{"username":"' . ESP8266DeviceFixtures::LOGIN_TEST_ACCOUNT_NAME['name'] . '","password":"' . ESP8266DeviceFixtures::LOGIN_TEST_ACCOUNT_NAME['password'] .'1"}'
//            );
//
//            self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
//        }
//
//        $this->client->request(
//            Request::METHOD_POST,
//            self::API_DEVICE_LOGIN,
//            [],
//            [],
//            ['CONTENT_TYPE' => 'application/json'],
//            '{"username":"' . ESP8266DeviceFixtures::LOGIN_TEST_ACCOUNT_NAME['name'] . '","password":"' . ESP8266DeviceFixtures::LOGIN_TEST_ACCOUNT_NAME['password'] .'"}'
//        );
//        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
//
//        $requestResponse = $this->client->getResponse();
//        $responseData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);
//
//        self::assertEquals('Too many failed login attempts, please try again in 1 minute.', $responseData['message']);
//    }
}
