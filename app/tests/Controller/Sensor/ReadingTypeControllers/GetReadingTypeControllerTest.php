<?php

namespace App\Tests\Controller\Sensor\ReadingTypeControllers;

use App\DataFixtures\Core\UserDataFixtures;
use App\Entity\Sensor\ReadingTypes\ReadingTypes;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Analog;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Humidity;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Latitude;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\Temperature;
use App\Entity\Sensor\Sensor;
use App\Repository\Sensor\SensorReadingType\ORM\ReadingTypeRepository;
use App\Services\Request\RequestTypeEnum;
use App\Tests\Controller\ControllerTestCase;
use App\Tests\Traits\TestLoginTrait;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetReadingTypeControllerTest extends ControllerTestCase
{
    private const GET_READING_TYPES_URL = '/HomeApp/api/user/reading-types';

    private const GET_SINGLE_READING_TYPE_URL = '/HomeApp/api/user/reading-types/%d';

    private ReadingTypeRepository $readingTypeRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->readingTypeRepository = $this->entityManager->getRepository(ReadingTypes::class);
    }

    public function test_getting_all_reading_types_invalid_token(): void
    {
        $this->client->jsonRequest(
            method: Request::METHOD_GET,
            uri: self::GET_READING_TYPES_URL,
            server: ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'Bearer safdgsaffsfdgsdfgfs']
        );

        $requestResponse = $this->client->getResponse();
        $responseData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(Response::HTTP_UNAUTHORIZED, $this->client->getResponse()->getStatusCode());
        self::assertEquals('Invalid JWT Token', $responseData['message']);
    }

    public function test_getting_all_reading_types(): void
    {
        $this->authenticateAdminOne();
        $this->client->jsonRequest(
            Request::METHOD_GET,
            self::GET_READING_TYPES_URL . '?responseType=' . RequestTypeEnum::FULL->value,
        );

        $requestResponse = $this->client->getResponse();
        $responseData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $readingTypeRepository = $this->entityManager->getRepository(ReadingTypes::class);

        /** @var ReadingTypes[] $allReadingTypes */
        $allReadingTypes = $readingTypeRepository->findAll();

        self::assertResponseIsSuccessful();
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

        $this->authenticateAdminOne();
        $this->client->jsonRequest(
            Request::METHOD_GET,
            sprintf(self::GET_SINGLE_READING_TYPE_URL, $readingTypeToTest->getReadingTypeID()) . '?responseType=' . RequestTypeEnum::FULL->value,
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

        $this->authenticateRegularUserOne();
        $this->client->jsonRequest(
            Request::METHOD_GET,
            sprintf(self::GET_SINGLE_READING_TYPE_URL, $readingTypeToTest->getReadingTypeID()) . '?responseType=' . RequestTypeEnum::FULL->value,
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
            Temperature::READING_TYPE,
        ];

        yield [
            Humidity::READING_TYPE,
        ];

        yield [
            Latitude::READING_TYPE,
        ];

        yield [
            Analog::READING_TYPE,
        ];
    }

//    /**
//     * @dataProvider wrongHttpsMethodDataProvider
//     */
//    public function test_using_wrong_http_method_singular(string $httpVerb): void
//    {
//        $sensorRepository = $this->entityManager->getRepository(Sensor::class);
//
//        /** @var Sensor[] $allSensors */
//        $allSensors = $sensorRepository->findAll();
//
//        $sensor = $allSensors[0];
//
//        $this->client->request(
//            $httpVerb,
//            sprintf(self::GET_SINGLE_READING_TYPE_URL, $sensor->getSensorID()),
//            [],
//            [],
//            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
//        );
//
//        self::assertEquals(Response::HTTP_METHOD_NOT_ALLOWED, $this->client->getResponse()->getStatusCode());
//    }

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
