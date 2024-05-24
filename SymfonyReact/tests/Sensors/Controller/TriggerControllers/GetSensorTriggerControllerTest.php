<?php

namespace App\Tests\Sensors\Controller\TriggerControllers;

use App\Common\Repository\TriggerTypeRepository;
use App\Devices\Entity\Devices;
use App\Devices\Repository\ORM\DeviceRepository;
use App\ORM\DataFixtures\Core\UserDataFixtures;
use App\Sensors\Controller\TriggerControllers\GetSensorTriggersController;
use App\Sensors\Entity\ReadingTypes\BaseSensorReadingType;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Relay;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTrigger;
use App\Sensors\Repository\ReadingType\ORM\BaseSensorReadingTypeRepository;
use App\Sensors\Repository\ReadingType\ORM\RelayRepository;
use App\Sensors\Repository\Sensors\ORM\SensorRepository;
use App\Sensors\Repository\SensorTriggerRepository;
use App\Tests\Traits\TestLoginTrait;
use App\User\Entity\Group;
use App\User\Entity\User;
use App\User\Repository\ORM\GroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use JsonException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetSensorTriggerControllerTest extends WebTestCase
{
    use TestLoginTrait;

    private const GET_ALL_SENSOR_TRIGGER_URL = '/HomeApp/api/user/sensor-trigger/all';

    private const GET_SENSOR_TRIGGER_URL = '/HomeApp/api/user/sensor-trigger/%d/get';

    private ?EntityManagerInterface $entityManager;

    private KernelBrowser $client;

    private ?string $userToken = null;

    private GroupRepository $groupRepository;

    private SensorRepository $sensorRepository;

    private DeviceRepository $deviceRepository;

    private RelayRepository $relayRepository;

    private BaseSensorReadingTypeRepository $baseSensorReadingTypeRepository;

    private SensorTriggerRepository $sensorTriggerRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->groupRepository = $this->entityManager->getRepository(Group::class);
        $this->sensorRepository = $this->entityManager->getRepository(Sensor::class);
        $this->deviceRepository = $this->entityManager->getRepository(Devices::class);
        $this->relayRepository = $this->entityManager->getRepository(Relay::class);
        $this->baseSensorReadingTypeRepository = $this->entityManager->getRepository(BaseSensorReadingType::class);
        $this->sensorTriggerRepository = $this->entityManager->getRepository(SensorTrigger::class);

        try {
            $this->userToken = $this->setUserToken($this->client);
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

    public function test_getting_all_the_triggers_for_user(): void
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_TWO]);
        $groupsUserIsApartOf = $this->groupRepository->findGroupsUserIsApartOf($user);
        $groupsUserIsNotApartOf = $this->groupRepository->findGroupsUserIsNotApartOf($user);

        $devicesUserIsApartOf = $this->deviceRepository->findBy(['groupID' => $groupsUserIsApartOf]);
        $devicesUserIsNotApartOf = $this->deviceRepository->findBy(['groupID' => $groupsUserIsNotApartOf]);

        $sensorsUserIsApartOf = $this->sensorRepository->findBy(['deviceID' => $devicesUserIsApartOf]);
        $sensorIDsUserIsApartOf = array_map(static function (Sensor $sensor) {
            return $sensor->getSensorID();
        }, $sensorsUserIsApartOf);
        $sensorsUserIsNotApartOf = $this->sensorRepository->findBy(['deviceID' => $devicesUserIsNotApartOf]);
        $sensorIDsUserIsNotApartOf = array_map(static function (Sensor $sensor) {
            return $sensor->getSensorID();
        }, $sensorsUserIsNotApartOf);

        $userToken = $this->setUserToken($this->client, $user->getEmail(), UserDataFixtures::REGULAR_PASSWORD);

        $this->client->request(
            Request::METHOD_GET,
            self::GET_ALL_SENSOR_TRIGGER_URL,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $userToken]
        );

        self::assertResponseIsSuccessful();

        $response = json_decode($this->client->getResponse()->getContent(), true);
        self::assertNotEmpty($response);

        $title = $response['title'];
        self::assertEquals(GetSensorTriggersController::REQUEST_SUCCESSFUL, $title);

        $payload = $response['payload'];
        self::assertNotEmpty($payload);

        foreach ($payload as $triggerResponseData) {
            self::assertArrayHasKey('sensorTriggerID', $triggerResponseData);
            self::assertArrayHasKey('valueThatTriggers', $triggerResponseData);
            self::assertArrayHasKey('triggerType', $triggerResponseData);
            self::assertArrayHasKey('baseReadingTypeThatTriggers', $triggerResponseData);
            self::assertArrayHasKey('baseReadingTypeThatIsTriggered', $triggerResponseData);
            self::assertArrayHasKey('createdAt', $triggerResponseData);
            self::assertArrayHasKey('createdBy', $triggerResponseData);
            self::assertArrayHasKey('endTime', $triggerResponseData);
            self::assertArrayHasKey('startTime', $triggerResponseData);
            self::assertArrayHasKey('monday', $triggerResponseData);
            self::assertArrayHasKey('tuesday', $triggerResponseData);
            self::assertArrayHasKey('wednesday', $triggerResponseData);
            self::assertArrayHasKey('thursday', $triggerResponseData);
            self::assertArrayHasKey('friday', $triggerResponseData);
            self::assertArrayHasKey('saturday', $triggerResponseData);
            self::assertArrayHasKey('sunday', $triggerResponseData);
            self::assertArrayHasKey('updatedAt', $triggerResponseData);

            if (!empty($triggerResponseData['baseReadingTypeThatIsTriggered']) && !empty($triggerResponseData['baseReadingTypeThatTriggers'])) {
                $responseBaseSensorIDThatIsTriggered = $triggerResponseData['baseReadingTypeThatIsTriggered']['sensor']['sensorID'];
                $responseBaseSensorIDThatTriggers = $triggerResponseData['baseReadingTypeThatTriggers']['sensor']['sensorID'];

                $shouldNotMatch = in_array($responseBaseSensorIDThatIsTriggered, $sensorIDsUserIsNotApartOf, true);
                $shouldNotMatchTwo = in_array($responseBaseSensorIDThatTriggers, $sensorIDsUserIsNotApartOf, true);

                self::assertFalse($shouldNotMatch && $shouldNotMatchTwo);
                $shouldMatch = in_array($responseBaseSensorIDThatIsTriggered, $sensorIDsUserIsApartOf, true);
                $shouldMatchTwo = in_array($responseBaseSensorIDThatTriggers, $sensorIDsUserIsApartOf, true);
                self::assertTrue($shouldMatch || $shouldMatchTwo);
            }
        }
    }

    public function test_admin_gets_all_triggers(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            self::GET_ALL_SENSOR_TRIGGER_URL,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $this->userToken]
        );

        self::assertResponseIsSuccessful();

        $response = json_decode($this->client->getResponse()->getContent(), true);
        self::assertNotEmpty($response);

        $title = $response['title'];
        self::assertEquals(GetSensorTriggersController::REQUEST_SUCCESSFUL, $title);

        $payload = $response['payload'];
        self::assertNotEmpty($payload);

        $allTriggers = $this->entityManager->getRepository(SensorTrigger::class)->findAll();
        self::assertCount(count($payload), $allTriggers);

        foreach ($payload as $triggerResponseData) {
            self::assertArrayHasKey('sensorTriggerID', $triggerResponseData);
            self::assertArrayHasKey('valueThatTriggers', $triggerResponseData);
            self::assertArrayHasKey('triggerType', $triggerResponseData);
            self::assertArrayHasKey('baseReadingTypeThatTriggers', $triggerResponseData);
            self::assertArrayHasKey('baseReadingTypeThatIsTriggered', $triggerResponseData);
            self::assertArrayHasKey('createdAt', $triggerResponseData);
            self::assertArrayHasKey('createdBy', $triggerResponseData);
            self::assertArrayHasKey('endTime', $triggerResponseData);
            self::assertArrayHasKey('startTime', $triggerResponseData);
            self::assertArrayHasKey('monday', $triggerResponseData);
            self::assertArrayHasKey('tuesday', $triggerResponseData);
            self::assertArrayHasKey('wednesday', $triggerResponseData);
            self::assertArrayHasKey('thursday', $triggerResponseData);
            self::assertArrayHasKey('friday', $triggerResponseData);
            self::assertArrayHasKey('saturday', $triggerResponseData);
            self::assertArrayHasKey('sunday', $triggerResponseData);
            self::assertArrayHasKey('updatedAt', $triggerResponseData);
        }
    }

    public function test_cannot_get_single_sensor_trigger_user_not_apart_of(): void
    {
        $regularUser = $this->findRegularUserOne($this->entityManager);

        $groupsUserIsNotApartOf = $this->groupRepository->findGroupsUserIsNotApartOf($regularUser);

        $devicesUserIsNotApartOf = $this->deviceRepository->findBy(['groupID' => $groupsUserIsNotApartOf]);

        $sensorsUserIsNotApartOf = $this->sensorRepository->findBy(['deviceID' => $devicesUserIsNotApartOf]);

        $baseReadingTypesBySensor = $this->baseSensorReadingTypeRepository->findBy(['sensor' => $sensorsUserIsNotApartOf]);

        $sensorTriggerUserIsNotApartOf = $this->sensorTriggerRepository->findBy(['baseReadingTypeThatTriggers' => $baseReadingTypesBySensor]);

        $sensorTriggerUserIsNotApartOf = $sensorTriggerUserIsNotApartOf[array_rand($sensorTriggerUserIsNotApartOf)];

        $userToken = $this->setUserToken($this->client, $regularUser->getEmail(), UserDataFixtures::REGULAR_PASSWORD);
        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::GET_SENSOR_TRIGGER_URL, $sensorTriggerUserIsNotApartOf->getSensorTriggerID()),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $userToken]
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function test_admin_can_get_any_single_sensor_triggers(): void
    {
        $sensorTrigger = $this->sensorTriggerRepository->findOneBy([]);

        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::GET_SENSOR_TRIGGER_URL, $sensorTrigger->getSensorTriggerID()),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $this->userToken]
        );

        self::assertResponseIsSuccessful();

        $response = json_decode($this->client->getResponse()->getContent(), true);
        self::assertNotEmpty($response);

        $title = $response['title'];
        self::assertEquals(GetSensorTriggersController::REQUEST_SUCCESSFUL, $title);

        $payload = $response['payload'];
        self::assertNotEmpty($payload);
    }

    public function test_response_payload_is_correct(): void
    {
        $sensorTrigger = $this->sensorTriggerRepository->findOneBy([]);

        $this->client->request(
            Request::METHOD_GET,
            sprintf(self::GET_SENSOR_TRIGGER_URL, $sensorTrigger->getSensorTriggerID()),
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $this->userToken]
        );

        self::assertResponseIsSuccessful();

        $response = json_decode($this->client->getResponse()->getContent(), true);
        self::assertNotEmpty($response);

        $title = $response['title'];
        self::assertEquals(GetSensorTriggersController::REQUEST_SUCCESSFUL, $title);

        $payload = $response['payload'];
        self::assertNotEmpty($payload);

        self::assertEquals($sensorTrigger->getSensorTriggerID(), $payload['sensorTriggerID']);
        self::assertEquals($sensorTrigger->getValueThatTriggers(), $payload['valueThatTriggers']);
        self::assertEquals($sensorTrigger->getTriggerType()->getTriggerTypeID(), $payload['triggerType']['triggerTypeID']);
        self::assertEquals($sensorTrigger->getBaseReadingTypeThatTriggers()->getBaseReadingTypeID(), $payload['baseReadingTypeThatTriggers']['baseReadingTypeID']);
        self::assertEquals($sensorTrigger->getBaseReadingTypeToTriggers()->getBaseReadingTypeID(), $payload['baseReadingTypeThatIsTriggered']['baseReadingTypeID']);
        self::assertEquals($sensorTrigger->getCreatedAt()->format('d-m-Y H:i:s'), $payload['createdAt']);
        self::assertEquals($sensorTrigger->getCreatedBy()->getEmail(), $payload['createdBy']['email']);
        self::assertEquals($sensorTrigger->getEndTime(), $payload['endTime']);
        self::assertEquals($sensorTrigger->getStartTime(), $payload['startTime']);
        self::assertEquals($sensorTrigger->getMonday(), $payload['monday']);
        self::assertEquals($sensorTrigger->getTuesday(), $payload['tuesday']);
        self::assertEquals($sensorTrigger->getWednesday(), $payload['wednesday']);
        self::assertEquals($sensorTrigger->getThursday(), $payload['thursday']);
        self::assertEquals($sensorTrigger->getFriday(), $payload['friday']);
        self::assertEquals($sensorTrigger->getSaturday(), $payload['saturday']);
        self::assertEquals($sensorTrigger->getSunday(), $payload['sunday']);
        self::assertEquals($sensorTrigger->getUpdatedAt()->format('d-m-Y H:i:s'), $payload['updatedAt']);
    }
}
