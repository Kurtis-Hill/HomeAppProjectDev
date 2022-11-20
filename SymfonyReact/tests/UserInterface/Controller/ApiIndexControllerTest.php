<?php

namespace UserInterface\Controller;

use App\Tests\Traits\TestLoginTrait;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiIndexControllerTest extends WebTestCase
{
    use TestLoginTrait;

    private const PING_URL = '/HomeApp/api/user/ping';

    private ?string $userToken = null;

    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->userToken = $this->setUserToken($this->client);
    }

    public function testLoggedInUserReceivesSuccessResponse(): void
    {
        $this->client->request(
            'GET',
            self::PING_URL,
            [],
            [],
            [
                'HTTP_Authorization' => 'Bearer ' . $this->userToken,
            ]
        );

        self::assertResponseIsSuccessful();
    }

    public function testUnauthenticatedUserReceivesAccessDeniedResponse(): void
    {
        $this->client->request(
            'GET',
            self::PING_URL,
        );

        $response = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertStringContainsString($response['message'], 'JWT Token not found');
        self::assertResponseStatusCodeSame(401);
    }

    public function testUserWithWrongTokenIsDeniedAccess(): void
    {
        $this->client->request(
            'GET',
            self::PING_URL,
            [],
            [],
            ['HTTP_Authorization' => 'Bearer ' . $this->userToken . 'wrong'],
        );

        $response = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertStringContainsString($response['message'], 'Invalid JWT Token');
        self::assertResponseStatusCodeSame(401);
    }
}
