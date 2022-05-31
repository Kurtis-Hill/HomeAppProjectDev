<?php

namespace UserInterface\Controller;

use App\Authentication\Controller\SecurityController;
use App\Doctrine\DataFixtures\Core\UserDataFixtures;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class IndexControllerTest extends WebTestCase
{
    private const INDEX_ROUTE_URL = '/HomeApp/WebApp/%s';

    private ?string $userToken = null;

    private ?EntityManagerInterface $entityManager;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->userToken = $this->setUserToken(UserDataFixtures::ADMIN_USER, UserDataFixtures::ADMIN_PASSWORD);
    }

    private function setUserToken(string $name, string $password): string
    {
        $this->client->request(
            Request::METHOD_POST,
            SecurityController::API_USER_LOGIN,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"username":"' . $name . '","password":"' . $password . '"}'
        );

        $requestResponse = $this->client->getResponse();
        $requestData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

        return $requestData['token'];
    }

    /**
     * @dataProvider variousRoutesDataProvider
     */
    public function test_various_routes_return_correct_response(string $uri): void
    {
        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::INDEX_ROUTE_URL, $uri),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
        );

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function variousRoutesDataProvider(): Generator
    {
        yield [
            'index',
        ];
        yield [
            'cards',
        ];
        yield [
            'navbar',
        ];
        yield [
            'sensors',
        ];
        yield [
            'devices',
        ];
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
        parent::tearDown();
    }
}
