<?php

namespace App\Tests\Controller;

use App\DataFixtures\Core\UserDataFixtures;
use App\Entity\User\User;
use App\Repository\User\ORM\UserRepository;
use App\Tests\Traits\TestLoginTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class DataStoreControllerTest extends WebTestCase
{
    private const ELASTIC_INDICES_URL = '/HomeApp/api/query/user';

    use TestLoginTrait;

    private ?EntityManagerInterface $entityManager;

    private KernelBrowser $client;

    private User $regularUserTwo;

    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->regularUserTwo = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_TWO]);
        $this->userRepository = $this->entityManager->getRepository(User::class);
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;

        parent::tearDown();
    }

    public function test_successful_elastic_indicies_request(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            self::ELASTIC_INDICES_URL,
        );
        self::assertResponseIsSuccessful();

        $response = $this->client->getResponse();
        $data = json_decode($response->getContent(), true);
        $payload = $data['payload'];

        self::assertCount(10, $payload);
    }
}
