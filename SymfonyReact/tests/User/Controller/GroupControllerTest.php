<?php

namespace App\Tests\User\Controller;

use App\Controller\Core\SecurityController;
use App\DataFixtures\Core\UserDataFixtures;
use App\Entity\Core\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class GroupControllerTest extends WebTestCase
{
    private const GET_USER_GROUPS_URL = '/HomeApp/api/user-groups/groups';

    private EntityManagerInterface $entityManager;

    private KernelBrowser $client;

    private string $userToken;

    private User $user;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::ADMIN_USER]);
        $this->setUserToken();
    }

    private function setUserToken(): void
    {
        $this->client->request(
            'POST',
            SecurityController::API_USER_LOGIN,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"username":"'.UserDataFixtures::ADMIN_USER.'","password":"'.UserDataFixtures::ADMIN_PASSWORD.'"}'
        );

        $requestResponse = $this->client->getResponse();
        $responseData = json_decode($requestResponse->getContent(), true);

        $this->userToken = $responseData['token'];
    }

    public function test_user_groups_are_correct(): void
    {
        $this->client->request(
            'GET',
            self::GET_USER_GROUPS_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer '.$this->userToken]
        );

        $requestResponse = $this->client->getResponse();
        $responseData = json_decode($requestResponse->getContent(), true);

        self::assertEquals(200, $requestResponse->getStatusCode());
        self::assertCount(2, $responseData);
        self::assertEquals(UserDataFixtures::ADMIN_GROUP, $responseData[0]['groupName']);
        self::assertEquals(UserDataFixtures::USER_GROUP, $responseData[1]['groupName']);
        self::assertIsNumeric($responseData[0]['groupNameId']);
        self::assertIsNumeric($responseData[1]['groupNameId']);
    }
}
