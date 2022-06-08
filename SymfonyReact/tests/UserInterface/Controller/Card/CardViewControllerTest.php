<?php

namespace UserInterface\Controller\Card;

use App\Authentication\Controller\SecurityController;
use App\Authentication\Entity\GroupNameMapping;
use App\Doctrine\DataFixtures\Core\RoomFixtures;
use App\Doctrine\DataFixtures\Core\UserDataFixtures;
use App\Sensors\Entity\ReadingTypes\Analog;
use App\Sensors\Entity\ReadingTypes\Humidity;
use App\Sensors\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;
use App\Sensors\Entity\ReadingTypes\Latitude;
use App\Sensors\Entity\ReadingTypes\Temperature;
use App\Sensors\Entity\Sensor;
use App\Sensors\Factories\SensorTypeQueryDTOFactory\SensorTypeQueryFactory;
use App\User\Entity\GroupNames;
use App\User\Entity\Room;
use App\User\Entity\User;
use App\UserInterface\DTO\Internal\CardDataQueryDTO\CardDataQueryEncapsulationFilterDTO;
use App\UserInterface\Entity\Card\Cardstate;
use App\UserInterface\Entity\Card\CardView;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class CardViewControllerTest extends WebTestCase
{
    public const CARD_VIEW_URL = '/HomeApp/api/user/card-data/%s';

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

        $this->groupName = $this->entityManager->getRepository(GroupNames::class)->findOneByName(UserDataFixtures::ADMIN_GROUP);
        $this->room = $this->entityManager->getRepository(Room::class)->findOneByRoomNameAndGroupNameId($this->groupName->getGroupNameID(), RoomFixtures::ADMIN_ROOM_NAME);
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
        $responseData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

        return $responseData['token'];
    }

    public function test_getting_all_card_data(): void
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

        self::assertEquals('Request Successful', $responseData['title']);
        self::assertIsArray($responseData['payload']);
        self::assertGreaterThan(1, count($responseData['payload']));

        $cardViewRepository = $this->entityManager->getRepository(CardView::class);
        $sensorRepository = $this->entityManager->getRepository(Sensor::class);

        foreach ($responseData['payload'] as $payload) {
            /** @var CardView $cardView */
            $cardViewObject = $cardViewRepository->findOneBy(['cardViewID' => $payload['cardViewID']]);

            self::assertEquals($cardViewObject->getCardViewID(), $payload['cardViewID']);
            self::assertEquals($cardViewObject->getSensorNameID()->getSensorName(), $payload['sensorName']);
            self::assertEquals($cardViewObject->getSensorNameID()->getSensorTypeObject()->getSensorType(), $payload['sensorType']);
            self::assertEquals($cardViewObject->getSensorNameID()->getDeviceObject()->getRoomObject()->getRoom(), $payload['sensorRoom']);
            self::assertEquals($cardViewObject->getCardIconID()->getIconName(), $payload['cardIcon']);
            self::assertEquals($cardViewObject->getCardColourID()->getColour(), $payload['cardColour']);

            $readingTypeQueryDTOs = $this->sensorTypeQueryFactory
                ->getSensorTypeQueryDTOBuilder($payload['sensorType'])
                ->buildSensorReadingTypes();

            $cardSensorReadingTypeObjects = $sensorRepository->getSensorTypeAndReadingTypeObjectsForSensor(
                $cardViewObject->getSensorNameID()->getDeviceObject()->getDeviceNameID(),
                $cardViewObject->getSensorNameID()->getSensorName(),
                null,
                $readingTypeQueryDTOs,
            );

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

//        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::ADMIN_USER]);
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



    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;

        parent::tearDown();
    }
}
