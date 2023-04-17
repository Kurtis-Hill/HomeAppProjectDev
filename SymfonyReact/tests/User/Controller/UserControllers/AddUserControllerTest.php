<?php

namespace App\Tests\User\Controller\UserControllers;

use App\Common\API\CommonURL;
use App\ORM\DataFixtures\Core\UserDataFixtures;
use App\Tests\Traits\TestLoginTrait;
use App\User\Controller\UserControllers\AddUserController;
use App\User\Entity\User;
use App\User\Repository\ORM\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AddUserControllerTest extends WebTestCase
{
    use TestLoginTrait;

    private const ADD_USER_URL = CommonURL::USER_HOMEAPP_API_URL . 'user/add';

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

        $this->regularUserTwo = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_TWO]);
        $this->userToken = $this->setUserToken($this->client);
        $this->userRepository = $this->entityManager->getRepository(User::class);
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
            self::ADD_USER_URL,
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
            [Request::METHOD_DELETE],
        ];
    }

    /**
     * @dataProvider wrongDataTypesDataProvider
     */
    public function test_adding_new_user_wrong_data_types(array $data, array $errorMessages): void
    {
        $jsonData = json_encode($data);

        $this->client->request(
            Request::METHOD_POST,
            self::ADD_USER_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
            $jsonData
        );
        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $response = json_decode($this->client->getResponse()->getContent(), true);

        $errors = $response['errors'];
        self::assertEquals($errorMessages, $errors);

        $title = $response['title'];
        self::assertEquals(AddUserController::BAD_REQUEST_NO_DATA_RETURNED, $title);
    }

    public function wrongDataTypesDataProvider(): Generator
    {
        yield [
            'data' => [
                'firstName' => 123,
                'lastName' => 'Doe',
                'email' => UserDataFixtures::UNIQUE_USER_EMAIL_NOT_TO_BE_USED,
                'groupName' => 'newGroupName',
                'password' => 'password',
                'roles' => ['ROLE_USER'],
            ],
            'errorMessages' => [
                'firstName must be a string you have provided 123',
            ]
        ];

        yield [
            'data' => [
                'firstName' => 'John',
                'lastName' => 123,
                'email' => UserDataFixtures::UNIQUE_USER_EMAIL_NOT_TO_BE_USED,
                'groupName' => 'newGroupName',
                'password' => 'password',
                'roles' => ['ROLE_USER'],
            ],
            'errorMessages' => [
                'lastName must be a string you have provided 123',
            ]
        ];

        yield [
            'data' => [
                'firstName' => 'John',
                'lastName' => 'Doe',
                'email' => 123,
                'groupName' => 'newGroupName',
                'password' => 'password',
                'roles' => ['ROLE_USER'],
            ],
            'errorMessages' => [
                'email must be a string you have provided 123',
            ]
        ];

        yield [
            'data' => [
                'firstName' => 'John',
                'lastName' => 'Doe',
                'email' => UserDataFixtures::UNIQUE_USER_EMAIL_NOT_TO_BE_USED,
                'groupName' => 123,
                'password' => 'password',
                'roles' => ['ROLE_USER'],
            ],
            'errorMessages' => [
                'groupName must be a string you have provided 123',
            ]
        ];

        yield [
            'data' => [
                'firstName' => 'John',
                'lastName' => 'Doe',
                'email' => UserDataFixtures::UNIQUE_USER_EMAIL_NOT_TO_BE_USED,
                'groupName' => 'newGroupName',
                'password' => 123,
                'roles' => ['ROLE_USER'],
            ],
            'errorMessages' => [
                'password must be a string you have provided 123',
            ]
        ];

        yield [
            'data' => [
                'firstName' => 'John',
                'lastName' => 'Doe',
                'email' => UserDataFixtures::UNIQUE_USER_EMAIL_NOT_TO_BE_USED,
                'groupName' => 'newGroupName',
                'password' => 'password',
                'roles' => 123,
            ],
            'errorMessages' => [
                'roles must be an array you have provided 123',
            ]
        ];

        yield [
            'data' => [
                'firstName' => [],
                'lastName' => 'Doe',
                'email' => UserDataFixtures::UNIQUE_USER_EMAIL_NOT_TO_BE_USED,
                'groupName' => 'newGroupName',
                'password' => 'password',
                'roles' => ['ROLE_USER'],
            ],
            'errorMessages' => [
                'firstName must be a string you have provided array',
            ]
        ];

        yield [
            'data' => [
                'firstName' => 'John',
                'lastName' => [],
                'email' => UserDataFixtures::UNIQUE_USER_EMAIL_NOT_TO_BE_USED,
                'groupName' => 'newGroupName',
                'password' => 'password',
                'roles' => ['ROLE_USER'],
            ],
            'errorMessages' => [
                'lastName must be a string you have provided array',
            ]
        ];

        yield [
            'data' => [
                'firstName' => 'John',
                'lastName' => 'Doe',
                'email' => [],
                'groupName' => 'newGroupName',
                'password' => 'password',
                'roles' => ['ROLE_USER'],
            ],
            'errorMessages' => [
                'email must be a string you have provided array',
            ]
        ];

        yield [
            'data' => [
                'firstName' => 'John',
                'lastName' => 'Doe',
                'email' => UserDataFixtures::UNIQUE_USER_EMAIL_NOT_TO_BE_USED,
                'groupName' => [],
                'password' => 'password',
                'roles' => ['ROLE_USER'],
            ],
            'errorMessages' => [
                'groupName must be a string you have provided array',
            ]
        ];

        yield [
            'data' => [
                'firstName' => 'John',
                'lastName' => 'Doe',
                'email' => UserDataFixtures::UNIQUE_USER_EMAIL_NOT_TO_BE_USED,
                'groupName' => 'newGroupName',
                'password' => [],
                'roles' => ['ROLE_USER'],
            ],
            'errorMessages' => [
                'password must be a string you have provided array',
            ]
        ];

        yield [
            'data' => [
                'lastName' => 'Doe',
                'email' => UserDataFixtures::UNIQUE_USER_EMAIL_NOT_TO_BE_USED,
                'groupName' => 'newGroupName',
                'password' => 'password',
                'roles' => ['ROLE_USER'],
            ],
            'errorMessages' => [
                'firstName cannot be null',
            ]
        ];

        yield [
            'data' => [
                'firstName' => 'John',
                'email' => UserDataFixtures::UNIQUE_USER_EMAIL_NOT_TO_BE_USED,
                'groupName' => 'newGroupName',
                'password' => 'password',
                'roles' => ['ROLE_USER'],
            ],
            'errorMessages' => [
                'lastName cannot be null',
            ]
        ];

        yield [
            'data' => [
                'firstName' => 'John',
                'lastName' => 'Doe',
                'groupName' => 'newGroupName',
                'password' => 'password',
                'roles' => ['ROLE_USER'],
            ],
            'errorMessages' => [
                'email cannot be null',
            ]
        ];

        yield [
            'data' => [
                'firstName' => 'John',
                'lastName' => 'Doe',
                'email' => UserDataFixtures::UNIQUE_USER_EMAIL_NOT_TO_BE_USED,
                'password' => 'password',
                'roles' => ['ROLE_USER'],
            ],
            'errorMessages' => [
                'groupName cannot be null',
            ]
        ];

        yield [
            'data' => [
                'firstName' => 'John',
                'lastName' => 'Doe',
                'email' => UserDataFixtures::UNIQUE_USER_EMAIL_NOT_TO_BE_USED,
                'groupName' => 'newGroupName',
                'roles' => ['ROLE_USER'],
            ],
            'errorMessages' => [
                'password cannot be null',
            ]
        ];
    }

    /**
     * @dataProvider outOfRangeDataProvider
     */
    public function test_sending_out_of_range_user_data(array $data, array $errorMessages): void
    {
        $jsonData = json_encode($data);

        $this->client->request(
            Request::METHOD_POST,
            self::ADD_USER_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
            $jsonData
        );
        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $response = json_decode($this->client->getResponse()->getContent(), true);

        $errors = $response['errors'];
        self::assertEquals($errorMessages, $errors);

        $title = $response['title'];
        self::assertEquals(AddUserController::BAD_REQUEST_NO_DATA_RETURNED, $title);
    }

    public function outOfRangeDataProvider(): Generator
    {
        yield [
            'data' => [
                'firstName' => 'J',
                'lastName' => 'Doe',
                'email' => UserDataFixtures::UNIQUE_USER_EMAIL_NOT_TO_BE_USED,
                'groupName' => 'newGroupName',
                'password' => 'password',
                'roles' => ['ROLE_USER'],
            ],
            'errorMessages' => [
                'First name must be at least 2 characters long',
            ]
        ];

        yield [
            'data' => [
                'firstName' => 'JsmirrJsmirrJsmirrJsmirrJsmirrJsmirr',
                'lastName' => 'Doe',
                'email' => UserDataFixtures::UNIQUE_USER_EMAIL_NOT_TO_BE_USED,
                'groupName' => 'newGroupName',
                'password' => 'password',
                'roles' => ['ROLE_USER'],
            ],
            'errorMessages' => [
                'First name cannot be longer than 20 characters',
            ]
        ];

        yield [
            'data' => [
                'firstName' => 'John',
                'lastName' => 'D',
                'email' => UserDataFixtures::UNIQUE_USER_EMAIL_NOT_TO_BE_USED,
                'groupName' => 'newGroupName',
                'password' => 'password',
                'roles' => ['ROLE_USER'],
            ],
            'errorMessages' => [
                'Last name must be at least 2 characters long',
            ]
        ];

        yield [
            'data' => [
                'firstName' => 'John',
                'lastName' => 'Ddsfsadfsdffsferferfree',
                'email' => UserDataFixtures::UNIQUE_USER_EMAIL_NOT_TO_BE_USED,
                'groupName' => 'newGroupName',
                'password' => 'password',
                'roles' => ['ROLE_USER'],
            ],
            'errorMessages' => [
                'Last name cannot be longer than 20 characters',
            ]
        ];

        yield [
            'data' => [
                'firstName' => 'John',
                'lastName' => 'Doe',
                'email' => 'notAnEmail',
                'groupName' => 'newGroupName',
                'password' => 'password',
                'roles' => ['ROLE_USER'],
            ],
            'errorMessages' => [
                'This value is not a valid email address.',
            ]
        ];

        yield [
            'data' => [
                'firstName' => 'John',
                'lastName' => 'Doe',
                'email' => UserDataFixtures::UNIQUE_USER_EMAIL_NOT_TO_BE_USED,
                'groupName' => 'newGroupName',
                'password' => 'pass',
                'roles' => ['ROLE_USER'],
            ],
            'errorMessages' => [
                'Password must be at least 8 characters long',
            ]
        ];

        yield [
            'data' => [
                'firstName' => 'John',
                'lastName' => 'Doe',
                'email' => UserDataFixtures::UNIQUE_USER_EMAIL_NOT_TO_BE_USED,
                'groupName' => 'newGroupName',
                'password' => 'passsdfds',
                'roles' => ['ROLE_SELECTED'],
            ],
            'errorMessages' => [
                'One or more of the given values is invalid.',
            ]
        ];

    }

    public function test_regular_user_cannot_create_a_new_user(): void
    {
        $jsonData = json_encode([
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => UserDataFixtures::UNIQUE_USER_EMAIL_NOT_TO_BE_USED,
            'groupName' => 'newGroupName',
            'password' => 'password',
        ]);

        $userToken = $this->setUserToken(
            $this->client,
            $this->regularUserTwo->getEmail(),
            UserDataFixtures::REGULAR_PASSWORD
        );
        $this->client->request(
            Request::METHOD_POST,
            self::ADD_USER_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $userToken],
            $jsonData
        );
        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        $user = $this->userRepository->findOneBy(['email' => UserDataFixtures::UNIQUE_USER_EMAIL_NOT_TO_BE_USED]);
        self::assertNull($user);
    }

    public function test_admin_user_can_create_a_new_user(): void
    {
        $jsonData = json_encode([
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => UserDataFixtures::UNIQUE_USER_EMAIL_NOT_TO_BE_USED,
            'groupName' => 'newGroupName',
            'password' => 'password',
        ]);

        $this->client->request(
            Request::METHOD_POST,
            self::ADD_USER_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
            $jsonData
        );
        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $response = json_decode($this->client->getResponse()->getContent(), true);

        $payload = $response['payload'];
        self::assertEquals('John', $payload['firstName']);
        self::assertEquals('Doe', $payload['lastName']);
        self::assertEquals(UserDataFixtures::UNIQUE_USER_EMAIL_NOT_TO_BE_USED, $payload['email']);
        self::assertArrayHasKey('group', $payload);

        $title = $response['title'];
        self::assertEquals(AddUserController::REQUEST_ACCEPTED_SUCCESS_CREATED, $title);

        $user = $this->userRepository->findOneBy(['email' => UserDataFixtures::UNIQUE_USER_EMAIL_NOT_TO_BE_USED]);
        self::assertInstanceOf(User::class, $user);
    }
}
