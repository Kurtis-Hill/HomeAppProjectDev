<?php

namespace User\Controller\GroupsController;

use App\Doctrine\DataFixtures\Core\UserDataFixtures;
use App\Authentication\Controller\SecurityController;
use App\User\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetGroupControllerTest extends WebTestCase
{
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
            Request::METHOD_GET,
            self::GET_USER_GROUPS_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer '.$this->userToken]
        );

        $requestResponse = $this->client->getResponse();
        $responseData = json_decode($requestResponse->getContent(), true);

        $count = 0;
        foreach ($responseData['payload'] as $payload) {
            self::assertEquals(UserDataFixtures::ALL_GROUPS[$count], $payload['groupName']);
            self::assertIsNumeric($payload['groupNameID']);
            ++$count;
        }
        self::assertEquals(Response::HTTP_OK, $requestResponse->getStatusCode());
        self::assertCount(2, $responseData);
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
        parent::tearDown();
    }
}
