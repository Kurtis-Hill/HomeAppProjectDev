<?php

namespace App\Tests\UserInterface\Controller\Card;

use App\Authentication\Controller\SecurityController;
use App\ORM\DataFixtures\Core\UserDataFixtures;
use App\Sensors\Controller\SensorControllers\UpdateSensorBoundaryReadingsController;
use App\Sensors\Entity\ReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;
use App\Sensors\Entity\ReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\ReadingTypes;
use App\Sensors\Entity\ReadingTypes\Temperature;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorType;
use App\Sensors\Factories\SensorTypeQueryDTOFactory\SensorTypeQueryFactory;
use App\Tests\Traits\TestLoginTrait;
use App\UserInterface\DTO\Internal\CardDataQueryDTO\JoinQueryDTO;
use App\UserInterface\Entity\Card\CardView;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetCardViewControllerTest extends WebTestCase
{
    use TestLoginTrait;

    public const CARD_VIEW_URL = '/HomeApp/api/user/cards/%s';

    private ?string $userToken = null;

    private ?EntityManagerInterface $entityManager;

    private KernelBrowser $client;

    private SensorTypeQueryFactory $sensorTypeQueryFactory;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->sensorTypeQueryFactory = static::getContainer()
            ->get(SensorTypeQueryFactory::class);

        $this->userToken = $this->setUserToken($this->client);
    }

    public function test_admin_getting_all_card_data(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::CARD_VIEW_URL, 'index'),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
        );

        $requestResponse = $this->client->getResponse();
        $responseData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(UpdateSensorBoundaryReadingsController::REQUEST_SUCCESSFUL, $responseData['title']);
        self::assertIsArray($responseData['payload']);
        self::assertGreaterThan(1, count($responseData['payload']));

        $cardViewRepository = $this->entityManager->getRepository(CardView::class);
        $sensorRepository = $this->entityManager->getRepository(Sensor::class);

        foreach ($responseData['payload'] as $payload) {
            /** @var CardView $cardViewObject */
            $cardViewObject = $cardViewRepository->findOneBy(['cardViewID' => $payload['cardViewID']]);

            self::assertEquals($cardViewObject->getCardViewID(), $payload['cardViewID']);
            self::assertEquals($cardViewObject->getSensor()->getSensorName(), $payload['sensorName']);
            self::assertEquals($cardViewObject->getSensor()->getSensorTypeObject()->getSensorType(), $payload['sensorType']);
            self::assertEquals($cardViewObject->getSensor()->getDevice()->getRoomObject()->getRoom(), $payload['sensorRoom']);
            self::assertEquals($cardViewObject->getCardIconID()->getIconName(), $payload['cardIcon']);
            self::assertEquals($cardViewObject->getCardColourID()->getColour(), $payload['cardColour']);

            $readingTypeQueryDTOs = $this->sensorTypeQueryFactory
                ->getSensorTypeQueryDTOBuilder($payload['sensorType'])
                ->buildSensorReadingTypes();

            /** @var Sensor[] $cardSensorReadingTypeObjects */
            $cardSensorReadingTypeObjects = $sensorRepository->getSensorTypeAndReadingTypeObjectsForSensor(
                $cardViewObject->getSensor()->getDevice()->getDeviceID(),
                $cardViewObject->getSensor()->getSensorName(),
                null,
                $readingTypeQueryDTOs,
            );

            self::assertNotEmpty($cardSensorReadingTypeObjects);
            $sensorDataArrayCount = 0;
            foreach ($cardSensorReadingTypeObjects as $cardSensorReadingTypeObject) {
                if ($cardSensorReadingTypeObject instanceof StandardReadingSensorInterface) {
                    if ($cardSensorReadingTypeObject instanceof Temperature) {
                        self::assertEquals(
                            Temperature::READING_TYPE,
                            $payload['sensorData'][$sensorDataArrayCount]['readingType']
                        );
                    }
                    if ($cardSensorReadingTypeObject instanceof Humidity) {
                        self::assertEquals(
                            Humidity::READING_TYPE,
                            $payload['sensorData'][$sensorDataArrayCount]['readingType']
                        );
                    }
                    if ($cardSensorReadingTypeObject instanceof Analog) {
                        self::assertEquals(
                            Analog::READING_TYPE,
                            $payload['sensorData'][$sensorDataArrayCount]['readingType']
                        );
                    }
                    if ($cardSensorReadingTypeObject instanceof Latitude) {
                        self::assertEquals(
                            Latitude::READING_TYPE,
                            $payload['sensorData'][$sensorDataArrayCount]['readingType']
                        );
                    }
                    self::assertEquals(
                        $cardSensorReadingTypeObject->getUpdatedAt()->format('d-m-Y H:i:s'),
                        $payload['sensorData'][$sensorDataArrayCount]['updatedAt']
                    );
                    self::assertEquals(
                        $cardSensorReadingTypeObject->getCurrentReading(),
                        $payload['sensorData'][$sensorDataArrayCount]['currentReading']
                    );
                    self::assertEquals(
                        $cardSensorReadingTypeObject->getHighReading(),
                        $payload['sensorData'][$sensorDataArrayCount]['highReading']
                    );
                    self::assertEquals(
                        $cardSensorReadingTypeObject->getLowReading(),
                        $payload['sensorData'][$sensorDataArrayCount]['lowReading']
                    );
                    if (isset($payload['sensorData'][$sensorDataArrayCount]['readingSymbol'])) {
                        self::assertEquals(
                            $cardSensorReadingTypeObject::getReadingSymbol(),
                            $payload['sensorData'][$sensorDataArrayCount]['readingSymbol']
                        );
                    }
                }
                ++$sensorDataArrayCount;
            }
        }

//        $user = $this->entityManager->getRepository(UserExceptions::class)->findOneBy(['email' => UserDataFixtures::ADMIN_USER]);
//        $groupNameMappingRepository = $this->entityManager->getRepository(GroupNameMapping::class);
//
//        $groupNameMappingEntities = $groupNameMappingRepository->getAllGroupMappingEntitiesForUser($user);
//        $user->setUserGroupMappingEntities($groupNameMappingEntities);
//
//        $cardViewAllCardSensorData = $cardViewRepository->getAllCardSensorData(
//            $user,
//            Cardstate::INDEX_ONLY,
//        );
//
//        dd($cardViewAllCardSensorData);
    }

    /**
     * @dataProvider noReadingTypeReturnedDataProvider
     */
    public function test_no_reading_type_returned_cards_get_returned(array $readingTypes): void
    {
        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::CARD_VIEW_URL, 'index'),
            ['reading-types' => $readingTypes],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER '.$this->userToken],
        );

        $requestResponse = $this->client->getResponse();
        $responseData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(UpdateSensorBoundaryReadingsController::REQUEST_SUCCESSFUL, $responseData['title']);
        self::assertIsArray($responseData['payload']);
        self::assertGreaterThan(1, count($responseData['payload']));

        $cardViewRepository = $this->entityManager->getRepository(CardView::class);
        $sensorRepository = $this->entityManager->getRepository(Sensor::class);

        foreach ($responseData['payload'] as $payload) {
            /** @var CardView $cardViewObject */
            $cardViewObject = $cardViewRepository->findOneBy(['cardViewID' => $payload['cardViewID']]);

            self::assertEquals($cardViewObject->getCardViewID(), $payload['cardViewID']);
            self::assertEquals($cardViewObject->getSensor()->getSensorName(), $payload['sensorName']);
            self::assertEquals($cardViewObject->getSensor()->getSensorTypeObject()->getSensorType(), $payload['sensorType']);
            self::assertEquals($cardViewObject->getSensor()->getDevice()->getRoomObject()->getRoom(), $payload['sensorRoom']);
            self::assertEquals($cardViewObject->getCardIconID()->getIconName(), $payload['cardIcon']);
            self::assertEquals($cardViewObject->getCardColourID()->getColour(), $payload['cardColour']);

            $readingTypeQueryDTOs = $this->sensorTypeQueryFactory
                ->getSensorTypeQueryDTOBuilder($payload['sensorType'])
                ->buildSensorReadingTypes();

            $arrayPlace = 0;
            foreach ($readingTypes as $readingType) {
                foreach ($readingTypeQueryDTOs as $readingTypeQueryDTO) {
                    /** @var JoinQueryDTO $readingTypeQueryDTO */
                    if ($readingTypeQueryDTO->getAlias() === ReadingTypes::SENSOR_READING_TYPE_DATA[$readingType]['alias']) {
                        unset($readingTypeQueryDTOs[$arrayPlace]);
                    }
                }
                ++$arrayPlace;
            }

            $cardSensorReadingTypeObjects = $sensorRepository->getSensorTypeAndReadingTypeObjectsForSensor(
                $cardViewObject->getSensor()->getDevice()->getDeviceID(),
                $cardViewObject->getSensor()->getSensorName(),
                null,
                $readingTypeQueryDTOs,
            );

            $sensorDataArrayCount = 0;
            foreach ($readingTypes as $readingType) {
                self::assertNotEquals($readingType, $payload['sensorData'][$sensorDataArrayCount]['readingType']);
            }
            foreach ($cardSensorReadingTypeObjects as $cardSensorReadingTypeObject) {
                if ($cardSensorReadingTypeObject instanceof StandardReadingSensorInterface) {
                    if ($cardSensorReadingTypeObject::getReadingTypeName() === $payload['sensorData'][$sensorDataArrayCount]['readingType']) {
                        self::assertEquals(
                            $cardSensorReadingTypeObject->getUpdatedAt()->format('d-m-Y H:i:s'),
                            $payload['sensorData'][$sensorDataArrayCount]['updatedAt']
                        );
                        self::assertEquals(
                            $cardSensorReadingTypeObject->getCurrentReading(),
                            $payload['sensorData'][$sensorDataArrayCount]['currentReading']
                        );
                        self::assertEquals(
                            $cardSensorReadingTypeObject->getHighReading(),
                            $payload['sensorData'][$sensorDataArrayCount]['highReading']
                        );
                        self::assertEquals(
                            $cardSensorReadingTypeObject->getLowReading(),
                            $payload['sensorData'][$sensorDataArrayCount]['lowReading']
                        );
                        if (isset($payload['sensorData'][$sensorDataArrayCount]['readingSymbol'])) {
                            self::assertEquals(
                                $cardSensorReadingTypeObject::getReadingSymbol(),
                                $payload['sensorData'][$sensorDataArrayCount]['readingSymbol']
                            );
                        }
                    }
                } else {
                    self::fail('Reading type not supported');
                }

                ++$sensorDataArrayCount;
            }
        }
    }

    public function noReadingTypeReturnedDataProvider(): Generator
    {
        yield [
            [
                Temperature::READING_TYPE,
            ]
        ];
        yield [
            [
                Humidity::READING_TYPE,
            ]
        ];
        yield [
            [
                Latitude::READING_TYPE,
            ]
        ];
        yield [
            [
                Analog::READING_TYPE,
            ]
        ];

        yield [
            [
                Temperature::READING_TYPE,
                Humidity::READING_TYPE,
            ]
        ];

        yield [
            [
                Temperature::READING_TYPE,
                Humidity::READING_TYPE,
                Latitude::READING_TYPE
            ]
        ];

        yield [
            [
                Temperature::READING_TYPE,
                Humidity::READING_TYPE,
                Latitude::READING_TYPE,
            ]
        ];

        yield [
            [
                Humidity::READING_TYPE,
                Latitude::READING_TYPE,
            ]
        ];

        yield [
            [
                Humidity::READING_TYPE,
                Analog::READING_TYPE
            ]
        ];

        yield [
            [
                Latitude::READING_TYPE,
                Analog::READING_TYPE
            ]
        ];
    }

    public function test_sending_all_reading_types_returns_error(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::CARD_VIEW_URL, 'index'),
            ['reading-types' => ReadingTypes::ALL_READING_TYPES],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );

        $requestResponse = $this->client->getResponse();

        $content = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $errors = $content['errors'];

        self::assertEquals('All reading types selected, please unselect some', $errors[0]);
        self::assertEquals(Response::HTTP_BAD_REQUEST, $requestResponse->getStatusCode());
    }

    public function test_sending_all_sensor_types_returns_error(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::CARD_VIEW_URL, 'index'),
            ['sensor-types' => SensorType::ALL_SENSOR_TYPES],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );

        $requestResponse = $this->client->getResponse();

        $content = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $errors = $content['errors'];

        self::assertEquals('All sensor types selected, please unselect some', $errors[0]);
        self::assertEquals(Response::HTTP_BAD_REQUEST, $requestResponse->getStatusCode());

    }
    
//@TODO add tests for device and room
    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;

        parent::tearDown();
    }
}
