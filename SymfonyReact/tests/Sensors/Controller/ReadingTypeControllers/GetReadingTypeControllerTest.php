<?php

namespace App\Tests\Sensors\Controller\ReadingTypeControllers;

use App\Doctrine\DataFixtures\Core\UserDataFixtures;
use App\Authentication\Controller\SecurityController;
use App\Sensors\Entity\ReadingTypes\ReadingTypes;
use App\Tests\Traits\TestLoginTrait;
use Doctrine\ORM\EntityManagerInterface;
use JsonException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class GetReadingTypeControllerTest extends WebTestCase
{
    use TestLoginTrait;

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

        $this->userToken = $this->setUserToken($this->client);
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
        parent::tearDown();
    }

    public function test_getting_all_reading_types(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            self::GET_READING_TYPES_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer '. $this->userToken]
        );

        $requestResponse = $this->client->getResponse();
        $responseData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $readingTypeRepository = $this->entityManager->getRepository(ReadingTypes::class);
        $allReadingTypes = $readingTypeRepository->findAll();

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertCount(count($allReadingTypes), $responseData['payload']);

        foreach ($responseData['payload'] as $readingType) {
            self::assertArrayHasKey('id', $readingType);
            self::assertArrayHasKey('readingType', $readingType);
        }
    }

}
