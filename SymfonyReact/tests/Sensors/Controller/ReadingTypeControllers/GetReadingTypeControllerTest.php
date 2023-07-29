<?php

namespace Sensors\Controller\ReadingTypeControllers;

use App\Doctrine\DataFixtures\Core\UserDataFixtures;
use App\Authentication\Controller\SecurityController;
use App\Sensors\Entity\ReadingTypes\ReadingTypes;
use Doctrine\ORM\EntityManagerInterface;
use JsonException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class GetReadingTypeControllerTest extends WebTestCase
{
    private const GET_READING_TYPES_URL = '/HomeApp/api/user/reading-types/all';

    private ?EntityManagerInterface $entityManager;

    private KernelBrowser $client;

    private ?string $userToken = null;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        try {
            $this->userToken = $this->setUserToken();
        } catch (JsonException $e) {
            error_log($e);
        }
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
        parent::tearDown();
    }

    private function setUserToken(bool $forceToken = false): string
    {
        if ($this->userToken === null || $forceToken === true) {
            $this->client->request(
                Request::METHOD_POST,
                SecurityController::API_USER_LOGIN,
                [],
                [],
                ['CONTENT_TYPE' => 'application/json'],
                '{"username":"'.UserDataFixtures::ADMIN_USER.'","password":"'.UserDataFixtures::ADMIN_PASSWORD.'"}'
            );

            $requestResponse = $this->client->getResponse();
            $responseData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

            return $responseData['token'];
        }

        return $this->userToken;
    }

    public function test_getting_all_reading_types(): void
    {

//        self::assertCount(count(ReadingTypes::SENSOR_READING_TYPE_DATA), $responseData['payload'])
    }

}
