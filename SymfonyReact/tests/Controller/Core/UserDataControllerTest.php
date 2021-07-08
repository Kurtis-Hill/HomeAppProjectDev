<?php


namespace App\Tests\Controller\Core;


use App\API\HTTPStatusCodes;
use App\Controller\Core\SecurityController;
use App\DataFixtures\Core\UserDataFixtures;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class UserDataControllerTest extends WebTestCase
{
    private const USER_DETAILS_ROUTE = '/HomeApp/api/user/account-details';

    /**
     * @var string|null
     */
    private ?string $userToken = null;

    /**
     * @var string|null
     */
    private ?string $userRefreshToken = null;

    /**
     * @var KernelBrowser
     */
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        try {
            $this->setUserToken();
        } catch (\JsonException $e) {
            error_log($e);
        }
    }

    /**
     * @return void
     * @throws \JsonException
     */
    private function setUserToken()
    {
        if ($this->userToken === null) {
            $this->client->request(
                'POST',
                SecurityController::API_USER_LOGIN,
                [],
                [],
                ['CONTENT_TYPE' => 'application/json'],
                '{"username":"'.UserDataFixtures::ADMIN_USER.'","password":"'.UserDataFixtures::ADMIN_PASSWORD.'"}'
            );

            $requestResponse = $this->client->getResponse();
            $requestData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

            $this->userToken = $requestData['token'];
            $this->userRefreshToken = $requestData['refreshToken'];
        }
    }

    /**
     * @param string|null $dataProvider
     * @param int $status
     * @param bool $success
     * @throws \JsonException
     * @dataProvider userDetailsDataProvider
     */
    public function test_getting_user_details(?string $dataProvider, int $status, bool $success): void
    {
        $this->client->request(
            'GET',
            self::USER_DETAILS_ROUTE,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken . $dataProvider],
        );

        if ($success !== false) {
            $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR)['payload'];
            self::assertArrayHasKey('userID', $responseData);
            self::assertArrayHasKey('roles', $responseData);
        }

        self::assertEquals($status, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @return \Generator
     */
    public function userDetailsDataProvider(): \Generator
    {
        yield [
            'token' => $this->userToken,
            'status' => HTTPStatusCodes::HTTP_OK,
            'succeed' => true
        ];

        yield [
            'token' => $this->userToken . '1',
            'status' => HTTPStatusCodes::HTTP_UNAUTHORISED,
            'succeed' => false
        ];
    }
}
