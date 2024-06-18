<?php

namespace App\Tests\Controller\User\UserControllers;

use App\Controller\Authentication\SecurityController;
use App\DataFixtures\Core\UserDataFixtures;
use App\Entity\User\Group;
use App\Entity\User\User;
use App\Exceptions\User\GroupExceptions\GroupNotFoundException;
use App\Exceptions\User\UserExceptions\IncorrectUserPasswordException;
use App\Repository\User\ORM\GroupRepositoryInterface;
use App\Repository\User\ORM\UserRepository;
use App\Services\API\CommonURL;
use App\Tests\Traits\TestLoginTrait;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateUserControllerTest extends WebTestCase
{
    use TestLoginTrait;

    private const UPDATE_USER_URL = CommonURL::USER_HOMEAPP_API_URL . '%d/update';

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

    /**
     * @dataProvider wrongHttpsMethodDataProvider
     */
    public function test_adding_device_wrong_http_method(string $httpVerb): void
    {
        $this->client->request(
            $httpVerb,
            self::UPDATE_USER_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->adminUserToken],
        );

        self::assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $this->client->getResponse()->getStatusCode());
    }

    public function wrongHttpsMethodDataProvider(): array
    {
        return [
            [Request::METHOD_POST],
            [Request::METHOD_GET],
            [Request::METHOD_DELETE],
        ];
    }

    public function test_updating_user_that_isnt_the_user_none_admin(): void
    {
        $regularUserOne = $this->userRepository->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_ONE]);

        $regularUserTwoToken = $this->setUserToken(
            $this->client,
            UserDataFixtures::REGULAR_USER_EMAIL_TWO,
            UserDataFixtures::REGULAR_PASSWORD
        );

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_USER_URL, $regularUserOne->getUserID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $regularUserTwoToken],
            json_encode([
                'email' => 'changeToThis@email.com'
            ])
        );

        self::assertEquals(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    public function test_updating_user_that_isnt_the_user_admin(): void
    {
        $regularUserOne = $this->userRepository->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_ONE]);

        $newEmail = 'changeME@email.com';

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_USER_URL, $regularUserOne->getUserID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->adminUserToken],
            json_encode([
                'email' => $newEmail
            ])
        );
        self::assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $updatedUser = $this->userRepository->findOneBy(['email' => $newEmail]);
        self::assertNotNull($updatedUser);
    }

//    public function test_updating_user_passwords_wrong_old_password(): void
//    {
//        $regularUserTwoToken = $this->setUserToken(
//            $this->client,
//            UserDataFixtures::REGULAR_USER_EMAIL_TWO,
//            UserDataFixtures::REGULAR_PASSWORD
//        );
//
//        $newPassword = 'newPassword';
//        $this->client->request(
//            Request::METHOD_PUT,
//            sprintf(self::UPDATE_USER_URL, $this->regularUserTwo->getUserID()),
//            [],
//            [],
//            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $regularUserTwoToken],
//            json_encode([
//                'newPassword' => $newPassword,
//                'oldPassword' => 'newPasswordConfirm'
//            ])
//        );
//
//        self::assert
//    }

    public function test_updating_user_roles_none_admin(): void
    {
        $regularUserTwoToken = $this->setUserToken(
            $this->client,
            UserDataFixtures::REGULAR_USER_EMAIL_TWO,
            UserDataFixtures::REGULAR_PASSWORD
        );

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_USER_URL, $this->regularUserTwo->getUserID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $regularUserTwoToken],
            json_encode([
                'roles' => ['ROLE_ADMIN']
            ])
        );

        self::assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());

        $updatedUser = $this->userRepository->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_TWO]);
        self::assertNotContains('ROLE_ADMIN', $updatedUser->getRoles());
    }

    public function test_updating_user_roles_admin(): void
    {
        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_USER_URL, $this->regularUserTwo->getUserID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->adminUserToken],
            json_encode([
                'roles' => ['ROLE_ADMIN']
            ])
        );

        self::assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $updatedUser = $this->userRepository->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_TWO]);
        self::assertContains('ROLE_ADMIN', $updatedUser->getRoles());
    }

    public function test_updating_users_base_group_none_admin(): void
    {
        $regularUserTwoToken = $this->setUserToken(
            $this->client,
            UserDataFixtures::REGULAR_USER_EMAIL_TWO,
            UserDataFixtures::REGULAR_PASSWORD
        );

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_USER_URL, $this->regularUserTwo->getUserID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $regularUserTwoToken],
            json_encode([
                'groupID' => 1
            ])
        );

        self::assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());

        $updatedUser = $this->userRepository->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_TWO]);
        self::assertNotEquals(1, $updatedUser->getUsersGroupName());
    }

    public function test_updating_users_base_group_admin(): void
    {
        /** @var \App\Entity\User\Group $anyGroup */
        $anyGroup = $this->entityManager->getRepository(Group::class)->findAll()[0];

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_USER_URL, $this->regularUserTwo->getUserID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->adminUserToken],
            json_encode([
                'groupID' => $anyGroup->getGroupID()
            ])
        );
        self::assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $updatedUser = $this->userRepository->findOneBy(['email' => $this->regularUserTwo->getEmail()]);
        self::assertEquals($anyGroup->getGroupID(), $updatedUser->getGroup()->getGroupID());

    }

    public function test_admin_changing_base_group_name_that_doesnt_exist(): void
    {
        $groupID = null;
        while ($groupID === null) {
            $randomInt = random_int(1, 10000);
            $group = $this->entityManager->getRepository(Group::class)->find($randomInt);
            if ($group === null) {
                $groupID = $randomInt;
            }
        }

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_USER_URL, $this->regularUserTwo->getUserID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->adminUserToken],
            json_encode([
                'groupID' => $groupID
            ])
        );

        self::assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());

        $response = json_decode($this->client->getResponse()->getContent(), true);
        $errors = $response['errors'];

        self::assertEquals(sprintf(GroupNotFoundException::MESSAGE, $groupID), $errors[0]);

        $updatedUser = $this->userRepository->findOneBy(['email' => $this->regularUserTwo->getEmail()]);
        self::assertNotEquals($groupID, $updatedUser->getGroup()->getGroupID());
    }

    /**
      * @dataProvider userOutOfRangeDataProvider
     */
    public function test_updating_users_data_out_of_range(
        mixed $firstName,
        mixed $lastName,
        mixed $email,
        mixed $roles,
        mixed $newPassword,
        mixed $oldPassword,
        mixed $groupID,
        mixed $errorMessage
    ): void {
        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_USER_URL, $this->regularUserTwo->getUserID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->adminUserToken],
            json_encode([
                'firstName' => $firstName,
                'lastName' => $lastName,
                'email' => $email,
                'roles' => $roles,
                'newPassword' => $newPassword,
                'oldPassword' => $oldPassword,
                'groupID' => $groupID
            ])
        );

        self::assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());

        $response = json_decode($this->client->getResponse()->getContent(), true);
        $errors = $response['errors'];

        self::assertEquals($errorMessage, $errors);

        $updatedUser = $this->userRepository->findOneBy(['email' => $this->regularUserTwo->getEmail()]);
        self::assertNotEquals($firstName, $updatedUser->getFirstName());
        self::assertNotEquals($lastName, $updatedUser->getLastName());
        self::assertNotEquals($email, $updatedUser->getEmail());
        self::assertNotEquals($roles, $updatedUser->getRoles());
        self::assertNotEquals($newPassword, $updatedUser->getPassword());
        self::assertNotEquals($oldPassword, $updatedUser->getPassword());
        self::assertNotEquals($groupID, $updatedUser->getGroup()->getGroupID());
    }

    public function userOutOfRangeDataProvider(): Generator
    {
        yield [
            'firstName' => [],
            'lastName' => 'lastName',
            'email' => 'email@email.com',
            'roles' => ['ROLES_ADMIN'],
            'newPassword' => 'newPassword',
            'oldPassword' => 'oldPassword',
            'groupID' => 1,
            'errorMessage' => [
                'firstName must be a string|null you have provided array'
            ],
        ];

        yield [
            'firstName' => 'firstName',
            'lastName' => [],
            'email' => 'email@email.com',
            'roles' => ['ROLES_ADMIN'],
            'newPassword' => 'newPassword',
            'oldPassword' => 'oldPassword',
            'groupID' => 1,
            'errorMessage' => [
                'lastName must be a string|null you have provided array',
            ],
        ];

        yield [
            'firstName' => 'firstName',
            'lastName' => 'lastName',
            'email' => [],
            'roles' => ['ROLES_ADMIN'],
            'newPassword' => 'newPassword',
            'oldPassword' => 'oldPassword',
            'groupID' => 1,
            'errorMessage' => [
                'email must be a string|null you have provided array',
            ],
        ];

        yield [
            'firstName' => 'firstName',
            'lastName' => 'lastName',
            'email' => 'email',
            'roles' => 'string',
            'newPassword' => 'newPassword',
            'oldPassword' => 'oldPassword',
            'groupID' => 1,
            'errorMessage' => [
                'roles must be a array|null you have provided "string"',
            ],
        ];

        yield [
            'firstName' => 'firstName',
            'lastName' => 'lastName',
            'email' => 'email',
            'roles' => ['ROLES_ADMIN'],
            'newPassword' => [],
            'oldPassword' => 'oldPassword',
            'groupID' => 1,
            'errorMessage' => [
                'newPassword must be a string|null you have provided array',
            ],
        ];

        yield [
            'firstName' => 'firstName',
            'lastName' => 'lastName',
            'email' => 'email',
            'roles' => ['ROLES_ADMIN'],
            'newPassword' => 'newPassword',
            'oldPassword' => [],
            'groupID' => 1,
            'errorMessage' => [
                'oldPassword must be a string|null you have provided array',
            ],
        ];

        yield [
            'firstName' => 'firstName',
            'lastName' => 'lastName',
            'email' => 'email',
            'roles' => ['ROLES_ADMIN'],
            'newPassword' => 'newPassword',
            'oldPassword' => 'oldPassword',
            'groupID' => [],
            'errorMessage' => [
                'groupID must be a int|null you have provided array',
            ],
        ];
    }

    /**
     * @dataProvider userValidationDataProvider
     */
    public function test_user_validation(
        mixed $firstName,
        mixed $lastName,
        mixed $email,
        mixed $roles,
        bool $groupID,
        array $errorMessage,
        mixed $newPassword = null,
        mixed $oldPassword = null,
    ): void {
        $formData = [];

        if ($firstName !== null) {
            $formData['firstName'] = $firstName;
        }
        if ($lastName !== null) {
            $formData['lastName'] = $lastName;
        }
        if ($email !== null) {
            $formData['email'] = $email;
        }
        if ($roles !== null) {
            $formData['roles'] = $roles;
        }
        if ($newPassword !== null) {
            $formData['newPassword'] = $newPassword;
        }
        if ($oldPassword !== null) {
            $formData['oldPassword'] = $oldPassword;
        }
        if ($groupID === true) {
            /** @var Group $newGroup */
            $newGroup = $this->groupRepository->findAll()[0];
            $formData['groupID'] = $newGroup->getGroupID();
        }

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_USER_URL, $this->regularUserTwo->getUserID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->adminUserToken],
            json_encode($formData)
        );

        self::assertEquals(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());

        $response = json_decode($this->client->getResponse()->getContent(), true);

        self::assertEquals($errorMessage, $response['errors']);
    }

    public function userValidationDataProvider(): Generator
    {

        yield [
            'firstName' => 'firstName',
            'lastName' => 'lastName',
            'email' => 'email@email.com',
            'roles' => ['ROLES_ADMIN'],
            'groupID' => false,
            'errorMessage' => [
                'Choose at least one valid role.',
//            'newPassword' => 'dasdadasdddd34',
//            'oldPassword' => UserDataFixtures::REGULAR_PASSWORD,
            ],
        ];

        yield [
            'firstName' => 'firstNamefirstNamefirstNamefirstName',
            'lastName' => 'lastName',
            'email' => 'email@email.com',
            'roles' => ['ROLE_ADMIN'],
            'groupID' => false,
            'errorMessage' => [
                'First name cannot be longer than 20 characters',
            ],
//            'newPassword' => 'newPassword',
//            'oldPassword' => 'oldPassword',
        ];

        yield [
            'firstName' => 'firstName',
            'lastName' => 'lastNamelastNamelastNamelas',
            'email' => 'email@emal.com',
            'roles' => ['ROLE_ADMIN'],
            'groupID' => false,
            'errorMessage' => [
                'Last name cannot be longer than 20 characters',
            ],
//            'newPassword' => 'newPassword',
//            'oldPassword' => 'oldPassword',
        ];

        yield [
            'firstName' => 'firstName',
            'lastName' => 'lastName',
            'email' => 'email',
            'roles' => ['ROLE_ADMIN'],
            'groupID' => false,
            'errorMessage' => [
                'This value is not a valid email address.',
            ],
//            'newPassword' => 'newPassword',
//            'oldPassword' => 'oldPassword',
        ];

//        yield [
//            'firstName' => 'firstName',
//            'lastName' => 'lastName',
//            'email' => 'email@email.com',
//            'roles' => ['ROLE_ADMIN'],
//            'groupID' => false,
//            'errorMessage' => [
//                'Password must be at least 8 characters long',
//            ],
//            'newPassword' => 'newPass',
//        ];

    }

    public function test_admin_can_change_password_without_specifying_old_password(): void
    {
        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_USER_URL, $this->regularUserTwo->getUserID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->adminUserToken],
            json_encode([
                'newPassword' => 'newPassword',
            ])
        );
        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $this->client->request(
            Request::METHOD_POST,
            SecurityController::API_USER_LOGIN,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'username' => $this->regularUserTwo->getEmail(),
                'password' => 'newPassword',
            ])
        );
        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function test_none_admin_users_need_to_specify_old_password(): void
    {
        $regularUserToken = $this->setUserToken(
            $this->client,
            $this->regularUserTwo->getEmail(),
            UserDataFixtures::REGULAR_PASSWORD
        );

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_USER_URL, $this->regularUserTwo->getUserID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $regularUserToken],
            json_encode([
                'newPassword' => 'newPassword',
            ])
        );
        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        self::assertEquals($responseData['errors'], [IncorrectUserPasswordException::MESSAGE]);

        $this->client->request(
            Request::METHOD_POST,
            SecurityController::API_USER_LOGIN,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'username' => $this->regularUserTwo->getEmail(),
                'password' => 'newPassword',
            ])
        );
        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function test_user_can_login_after_password_change(): void
    {
        $newPassword = 'newPassword2343323HAHA123';
        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_USER_URL, $this->regularUserTwo->getUserID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->adminUserToken],
            json_encode([
                'firstName' => 'firstName',
                'lastName' => 'lastName',
                'email' => $this->regularUserTwo->getEmail(),
                'roles' => ['ROLE_ADMIN'],
                'newPassword' => $newPassword,
            ])
        );
        self::assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $this->client->request(
            Request::METHOD_POST,
            SecurityController::API_USER_LOGIN,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'username' => $this->regularUserTwo->getEmail(),
                'password' => $newPassword,
            ])
        );
        self::assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function test_update_user_response(): void
    {
        /** @var \App\Entity\User\Group $newGroup */
        $newGroup = $this->groupRepository->findAll()[0];

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_USER_URL, $this->regularUserTwo->getUserID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->adminUserToken],
            json_encode([
                'firstName' => 'firstName',
                'lastName' => 'lastName',
                'email' => 'email@email.com',
                'roles' => ['ROLE_ADMIN'],
                'groupID' => $newGroup->getGroupID(),
            ])
        );

        self::assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $response = json_decode($this->client->getResponse()->getContent(), true);

        $payload = $response['payload'];

        self::assertEquals('firstName', $payload['firstName']);
        self::assertEquals('lastName', $payload['lastName']);
        self::assertEquals('email@email.com', $payload['email']);
        self::assertEquals(['ROLE_ADMIN'], $payload['roles']);
        self::assertEquals($newGroup->getGroupID(), $payload['group']['groupID']);
        self::assertEquals($newGroup->getGroupName(), $payload['group']['groupName']);
        self::assertArrayHasKey('userID', $payload);
        self::assertArrayHasKey('createdAt', $payload);
    }
}
