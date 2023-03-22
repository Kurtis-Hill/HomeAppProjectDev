<?php

namespace App\Tests\Sensors\Controller\ReadingTypeControllers;

use App\ORM\DataFixtures\Core\UserDataFixtures;
use App\Sensors\Entity\ReadingTypes\ReadingTypes;
use App\Sensors\Entity\Sensor;
use App\Sensors\Repository\SensorReadingType\ORM\ReadingTypeRepository;
use App\Tests\Traits\TestLoginTrait;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetReadingTypeControllerTest extends WebTestCase
{
    use TestLoginTrait;

    private const GET_READING_TYPES_URL = '/HomeApp/api/user/reading-types/all';

    private const GET_SINGLE_READING_TYPE_URL = '/HomeApp/api/user/reading-types/%d';

    private ?EntityManagerInterface $entityManager;

    private ReadingTypeRepository $readingTypeRepository;

    private KernelBrowser $client;

    private ?string $userToken = null;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->readingTypeRepository = $this->entityManager->getRepository(ReadingTypes::class);

        $this->userToken = $this->setUserToken($this->client);
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
        parent::tearDown();
    }

    public function test_getting_all_reading_types_invalid_token(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            self::GET_READING_TYPES_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer '. $this->userToken . '1']
        );

        $requestResponse = $this->client->getResponse();
        $responseData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
        self::assertEquals('Invalid JWT Token', $responseData['message']);

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

        /** @var ReadingTypes[] $allReadingTypes */
        $allReadingTypes = $readingTypeRepository->findAll();

        self::assertEquals(200, $this->client->getResponse()->getStatusCode());
        self::assertCount(count($allReadingTypes), $responseData['payload']);

        foreach ($responseData['payload'] as $readingType) {
            self::assertArrayHasKey('readingTypeID', $readingType);
            self::assertArrayHasKey('readingTypeName', $readingType);
        }
    }

    /**
     * @dataProvider singleReadingTypeNamesDataProvider
     */
    public function test_getting_single_reading_type_admin_user(string $readingTypeName): void
    {
        /** @var ReadingTypes $readingTypes */
        $readingTypeToTest = $this->readingTypeRepository->findOneBy(
            ['readingType' => $readingTypeName]
        );

        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::GET_SINGLE_READING_TYPE_URL, $readingTypeToTest->getReadingTypeID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer '. $this->userToken]
        );

        $requestResponse = $this->client->getResponse();
        $responseData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $readingTypeRepository = $this->entityManager->getRepository(ReadingTypes::class);

        /** @var ReadingTypes $readingType */
        $readingType = $readingTypeRepository->find($readingTypeToTest->getReadingTypeID());

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        self::assertEquals($readingType->getReadingType(), $responseData['payload']['readingTypeName']);
        self::assertEquals($readingType->getReadingTypeID(), $responseData['payload']['readingTypeID']);
    }

    /**
     * @dataProvider singleReadingTypeNamesDataProvider
     */
    public function test_getting_single_reading_type_admin_regular_user(string $readingTypeName): void
    {
        /** @var ReadingTypes $readingTypes */
        $readingTypeToTest = $this->readingTypeRepository->findOneBy(
            ['readingType' => $readingTypeName]
        );

        $userToken = $this->setUserToken(
            $this->client,
            UserDataFixtures::REGULAR_USER_EMAIL_ONE,
            UserDataFixtures::REGULAR_PASSWORD
        );

        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::GET_SINGLE_READING_TYPE_URL, $readingTypeToTest->getReadingTypeID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer '. $userToken]
        );

        $requestResponse = $this->client->getResponse();
        $responseData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $readingTypeRepository = $this->entityManager->getRepository(ReadingTypes::class);

        /** @var ReadingTypes $readingType */
        $readingType = $readingTypeRepository->find($readingTypeToTest->getReadingTypeID());

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        self::assertEquals($readingType->getReadingType(), $responseData['payload']['readingTypeName']);
        self::assertEquals($readingType->getReadingTypeID(), $responseData['payload']['readingTypeID']);
    }

    public function singleReadingTypeNamesDataProvider(): Generator
    {
        yield [
            'temperature',
        ];

        yield [
            'humidity',
        ];

        yield [
            'latitude',
        ];

        yield [
            'analog',
        ];
    }

    /**
     * @dataProvider wrongHttpsMethodDataProvider
     */
    public function test_using_wrong_http_method_singular(string $httpVerb): void
    {
        $sensorRepository = $this->entityManager->getRepository(Sensor::class);

        /** @var Sensor[] $allSensors */
        $allSensors = $sensorRepository->findAll();

        $sensor = $allSensors[0];

        $this->client->request(
            $httpVerb,
            sprintf(self::GET_SINGLE_READING_TYPE_URL, $sensor->getSensorID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );

        self::assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $this->client->getResponse()->getStatusCode());
    }

    public function wrongHttpsMethodDataProvider(): array
    {
        return [
            [Request::METHOD_POST],
            [Request::METHOD_PUT],
            [Request::METHOD_PATCH],
            [Request::METHOD_DELETE],
        ];
    }
}
