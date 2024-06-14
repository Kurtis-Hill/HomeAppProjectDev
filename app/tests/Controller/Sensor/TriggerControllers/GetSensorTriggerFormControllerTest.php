<?php

namespace App\Tests\Controller\Sensor\TriggerControllers;

use App\DataFixtures\Core\UserDataFixtures;
use App\Entity\Common\Operator;
use App\Entity\Device\Devices;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Relay;
use App\Entity\Sensor\Sensor;
use App\Entity\Sensor\TriggerType;
use App\Entity\User\Group;
use App\Entity\User\User;
use App\Repository\Device\ORM\DeviceRepository;
use App\Repository\Sensor\ReadingType\ORM\RelayRepository;
use App\Repository\Sensor\Sensors\ORM\SensorRepository;
use App\Repository\User\ORM\GroupRepository;
use App\Tests\Traits\TestLoginTrait;
use Doctrine\ORM\EntityManagerInterface;
use JsonException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetSensorTriggerFormControllerTest extends WebTestCase
{
    use TestLoginTrait;

    private const GET_SENSOR_TRIGGER_FORM_URL = '/HomeApp/api/user/sensor-trigger/form/get';

    private ?EntityManagerInterface $entityManager;

    private KernelBrowser $client;

    private ?string $userToken = null;

    private GroupRepository $groupRepository;

    private SensorRepository $sensorRepository;

    private DeviceRepository $deviceRepository;

    private RelayRepository $relayRepository;

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

    public function test_sensors_user_should_not_see_dont_appear(): void
    {
        /** @var User $regularUser */
        $regularUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_ONE]);
        $userToken = $this->setUserToken($this->client, $regularUser->getEmail(), UserDataFixtures::REGULAR_PASSWORD);

        $groupsUserIsNotApartOf = $this->groupRepository->findGroupsUserIsNotApartOf($regularUser);

        $devicesUserIsNotApartOf = $this->deviceRepository->findBy(['groupID' => $groupsUserIsNotApartOf]);
        $sensorsUserIsNotApartOf = $this->sensorRepository->findBy(['deviceID' => $devicesUserIsNotApartOf]);

        $this->client->request(
            Request::METHOD_GET,
            self::GET_SENSOR_TRIGGER_FORM_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $userToken],
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $payload = $responseData['payload'];
        self::assertNotEmpty($payload);

        $operators = $payload['operators'];
        $triggerTypes = $payload['triggerTypes'];
        $sensors = $payload['sensors'];

        self::assertNotEmpty($operators);
        self::assertNotEmpty($triggerTypes);
        self::assertNotEmpty($sensors);

        $sensorIDs = array_map(static function ($sensor) {
            return $sensor['sensorID'];
        }, $sensors);

        foreach ($sensorsUserIsNotApartOf as $sensorNotApartOf) {
            self::assertNotContains($sensorNotApartOf->getSensorID(), $sensorIDs);
        }

        self::assertCount(count(Operator::ALL_OPERATORS), $operators);

        self::assertCount(count(TriggerType::ALL_TRIGGER_TYPES), $triggerTypes);
    }

    public function test_relay_sensors_user_should_not_see_dont_appear(): void
    {
        /** @var User $regularUser */
        $regularUser = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_ONE]);
        $userToken = $this->setUserToken($this->client, $regularUser->getEmail(), UserDataFixtures::REGULAR_PASSWORD);

        $groupsUserIsNotApartOf = $this->groupRepository->findGroupsUserIsNotApartOf($regularUser);

        $this->client->request(
            Request::METHOD_GET,
            self::GET_SENSOR_TRIGGER_FORM_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $userToken],
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        $payload = $responseData['payload'];
        self::assertNotEmpty($payload);

        $relays = $payload['relays'];
        self::assertNotEmpty($relays);

        $relayIDs = array_map(static function ($relay) {
            return $relay['baseReadingTypeID'];
        }, $relays);

        /** @var Relay[] $relaysUserIsNotApartOf */
        $relaysUserIsNotApartOf = $this->relayRepository->findReadingTypeUserHasAccessTo($groupsUserIsNotApartOf);
        foreach ($relaysUserIsNotApartOf as $relayNotApartOf) {
            self::assertNotEquals($relayNotApartOf->getBaseReadingType()->getBaseReadingTypeID(), $relayIDs);
        }
    }

    public function test_admin_full_response_should_see_all(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            self::GET_SENSOR_TRIGGER_FORM_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        $payload = $responseData['payload'];
        self::assertNotEmpty($payload);

        $operators = $payload['operators'];
        $triggerTypes = $payload['triggerTypes'];
        $sensors = $payload['sensors'];
        $relays = $payload['relays'];

        self::assertNotEmpty($operators);
        self::assertNotEmpty($triggerTypes);
        self::assertNotEmpty($sensors);
        self::assertNotEmpty($relays);

        $allRelays = $this->relayRepository->findAll();
        $allSensors = $this->sensorRepository->findAll();

        $allRelayIDs = array_map(static function ($relay) {
            return $relay->getBaseReadingType()->getBaseReadingTypeID();
        }, $allRelays);

        $allSensorIDs = array_map(static function ($sensor) {
            return $sensor->getSensorID();
        }, $allSensors);

        $allRelayIDsFromResponse = array_map(static function ($relay) {
            return $relay['baseReadingTypeID'];
        }, $relays);
        foreach ($allRelayIDs as $relayID) {
            self::assertContains($relayID, $allRelayIDsFromResponse);
        }

        $allSensorIDsFromResponse = array_map(static function ($sensor) {
            return $sensor['sensorID'];
        }, $sensors);

        foreach ($allSensorIDs as $sensorID) {
            self::assertContains($sensorID, $allSensorIDsFromResponse);
        }

        self::assertCount(count(Operator::ALL_OPERATORS), $operators);
        self::assertCount(count(TriggerType::ALL_TRIGGER_TYPES), $triggerTypes);
    }

    /**
     * @dataProvider wrongHttpsMethodDataProvider
     */
    public function test_using_wrong_http_method(string $httpVerb): void
    {
        $this->client->request(
            $httpVerb,
            self::GET_SENSOR_TRIGGER_FORM_URL,
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
