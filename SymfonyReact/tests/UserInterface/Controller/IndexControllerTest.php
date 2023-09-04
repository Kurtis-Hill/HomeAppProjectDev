<?php

namespace App\Tests\UserInterface\Controller;

use App\Authentication\Controller\SecurityController;
use App\ORM\DataFixtures\Core\UserDataFixtures;
use App\Tests\Traits\TestLoginTrait;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IndexControllerTest extends WebTestCase
{
    use TestLoginTrait;

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

        $this->userToken = $this->setUserToken($this->client);
    }

    /**
     * @dataProvider variousRoutesDataProvider
     */
    public function test_various_routes_return_forbidden_response_no_credentials(string $uri): void
    {
        self::markTestSkipped('skipped until firewall reactivation');
        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::INDEX_ROUTE_URL, $uri),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
        );
        self::assertEquals(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
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
