<?php

namespace App\Tests\User\Controller\UserControllers;

use App\Common\API\CommonURL;
use App\ORM\DataFixtures\Core\UserDataFixtures;
use App\Tests\Traits\TestLoginTrait;
use App\User\Entity\Group;
use App\User\Entity\User;
use App\User\Repository\ORM\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UpdateUserControllerTest extends WebTestCase
{
    use TestLoginTrait;

    private const UPDATE_USER_URL = CommonURL::USER_HOMEAPP_API_URL . 'user/%d/update';

    private ?EntityManagerInterface $entityManager;

    private KernelBrowser $client;

    private User $regularUserTwo;

    private UserRepository $userRepository;

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

    /**
      * @dataProvider wrongDataTypesProvider
     */
    public function test_sending_request_wrong_data_types(): void
    {

    }

    public function wrongDataTypesProvider(): Generator
    {
        yield [];
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

        self::assertEquals(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());

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

        self::assertEquals(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());

        $updatedUser = $this->userRepository->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_TWO]);
        self::assertNotEquals(1, $updatedUser->getUsersGroupName());
    }

    public function test_updating_users_base_group_admin(): void
    {
        /** @var Group $anyGroupName */
        $anyGroupName = $this->entityManager->getRepository(Group::class)->findAll()[0];

        $this->client->request(
            Request::METHOD_PUT,
            sprintf(self::UPDATE_USER_URL, $this->regularUserTwo->getUserID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->adminUserToken],
            json_encode([
                'groupID' => $anyGroupName->getGroupID()
            ])
        );

        self::assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());

        $updatedUser = $this->userRepository->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_TWO]);
        self::assertEquals($anyGroupName->getGroupID(), $updatedUser->getUsersGroupName());

    }

    public function test_admin_changing_base_group_name_that_doesnt_exist(): void
    {

    }

    /**
      * @dataProvider userOutOfRangeDataProvider
     */
    public function test_updating_users_data_out_of_range(): void
    {

    }

    public function userOutOfRangeDataProvider(): Generator
    {
        yield [];
    }

    public function test_update_user_response(): void
    {

    }
}
