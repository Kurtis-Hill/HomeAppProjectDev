<?php

namespace App\Tests\Controller\Sensor\TriggerControllers;

use App\DataFixtures\Core\UserDataFixtures;
use App\Entity\Device\Devices;
use App\Entity\Sensor\ReadingTypes\BaseSensorReadingType;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\Relay;
use App\Entity\Sensor\Sensor;
use App\Entity\Sensor\SensorTrigger;
use App\Entity\User\Group;
use App\Entity\User\User;
use App\Repository\Device\ORM\DeviceRepository;
use App\Repository\Sensor\ReadingType\ORM\BaseSensorReadingTypeRepository;
use App\Repository\Sensor\ReadingType\ORM\RelayRepository;
use App\Repository\Sensor\Sensors\ORM\SensorRepository;
use App\Repository\Sensor\SensorTriggerRepository;
use App\Repository\User\ORM\GroupRepository;
use App\Repository\User\ORM\UserRepository;
use App\Tests\Traits\TestLoginTrait;
use Doctrine\ORM\EntityManagerInterface;
use JsonException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DeleteTriggerControllerTest extends WebTestCase
{
    use TestLoginTrait;

    private const DELETE_SENSOR_TRIGGER_URL = '/HomeApp/api/user/sensor-trigger/%d/delete';

    private ?EntityManagerInterface $entityManager;

    private KernelBrowser $client;

    private ?string $userToken = null;

    private UserRepository $userRepository;

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
        $this->userRepository = $this->entityManager->getRepository(User::class);
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

    public function test_regular_user_cannot_delete_trigger_not_apart_of(): void
    {
        $regularUser = $this->userRepository->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_ONE]);

        /** @var SensorTrigger[] $sensorTriggerResult */
        $sensorTriggerResult = $this->sensorTriggerRepository->findAll();

        $usersGroups = $regularUser->getAssociatedGroupIDs();

        foreach ($sensorTriggerResult as $trigger) {
            $baseReadingTypeThatTriggers = $trigger->getBaseReadingTypeThatTriggers();
            $baseReadingTypeToTrigger = $trigger->getBaseReadingTypeToTriggers();

            $toTriggerGroupID = $baseReadingTypeToTrigger->getSensor()->getDevice()->getGroupObject()->getGroupID();
            $triggeredGroupID = $baseReadingTypeThatTriggers->getSensor()->getDevice()->getGroupObject()->getGroupID();

            if (!in_array($toTriggerGroupID, $usersGroups, true) && !in_array($triggeredGroupID, $usersGroups, true)) {
                $sensorTrigger = $trigger;
                break;
            }
        }

        if (!isset($sensorTrigger)) {
            $this->hasFailed('No sensor trigger found');
        }

        $userToken = $this->setUserToken($this->client, UserDataFixtures::REGULAR_USER_EMAIL_ONE, UserDataFixtures::REGULAR_PASSWORD);
        $this->client->request(
            Request::METHOD_DELETE,
            sprintf(self::DELETE_SENSOR_TRIGGER_URL, $sensorTrigger->getSensorTriggerID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $userToken],
        );

        self::assertEquals(Response::HTTP_FORBIDDEN, $this->client->getResponse()->getStatusCode());
    }

    public function test_regular_user_can_delete_trigger_apart_of(): void
    {
        $regularUser = $this->userRepository->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_TWO]);

        /** @var SensorTrigger[] $sensorTriggerResult */
        $sensorTriggerResult = $this->sensorTriggerRepository->findAll();

        $usersGroups = $regularUser->getAssociatedGroupIDs();

        foreach ($sensorTriggerResult as $trigger) {
            $baseReadingTypeThatTriggers = $trigger->getBaseReadingTypeThatTriggers();
            $baseReadingTypeToTrigger = $trigger->getBaseReadingTypeToTriggers();

            $toTriggerGroupID = $baseReadingTypeToTrigger?->getSensor()->getDevice()->getGroupObject()->getGroupID();
            $triggeredGroupID = $baseReadingTypeThatTriggers?->getSensor()->getDevice()->getGroupObject()->getGroupID();

            if (in_array($toTriggerGroupID, $usersGroups, true) && in_array($triggeredGroupID, $usersGroups, true)) {
                $sensorTrigger = $trigger;
                break;
            }
        }

        if (!isset($sensorTrigger)) {
            $this->hasFailed('No sensor trigger found');
        }

        $userToken = $this->setUserToken($this->client, UserDataFixtures::REGULAR_USER_EMAIL_TWO, UserDataFixtures::REGULAR_PASSWORD);
        $this->client->request(
            Request::METHOD_DELETE,
            sprintf(self::DELETE_SENSOR_TRIGGER_URL, $sensorTrigger->getSensorTriggerID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $userToken],
        );

        self::assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    public function test_admin_user_can_delete_trigger_not_apart_of(): void
    {
        $adminUser = $this->userRepository->findOneBy(['email' => UserDataFixtures::ADMIN_USER_EMAIL_TWO]);

        /** @var SensorTrigger[] $sensorTriggerResult */
        $sensorTriggerResult = $this->sensorTriggerRepository->findAll();

        $usersGroups = $adminUser->getAssociatedGroupIDs();

        foreach ($sensorTriggerResult as $trigger) {
            $baseReadingTypeThatTriggers = $trigger->getBaseReadingTypeThatTriggers();
            $baseReadingTypeToTrigger = $trigger->getBaseReadingTypeToTriggers();

            $toTriggerGroupID = $baseReadingTypeToTrigger?->getSensor()->getDevice()->getGroupObject()->getGroupID();
            $triggeredGroupID = $baseReadingTypeThatTriggers?->getSensor()->getDevice()->getGroupObject()->getGroupID();

            if (!in_array($toTriggerGroupID, $usersGroups, true) && !in_array($triggeredGroupID, $usersGroups, true)) {
                $sensorTrigger = $trigger;
                break;
            }
        }

        if (!isset($sensorTrigger)) {
            $this->hasFailed('No sensor trigger found');
        }

        $this->client->request(
            Request::METHOD_DELETE,
            sprintf(self::DELETE_SENSOR_TRIGGER_URL, $sensorTrigger->getSensorTriggerID()),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json', 'HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
        );

        self::assertEquals(Response::HTTP_OK, $this->client->getResponse()->getStatusCode());
    }

    /**
     * @dataProvider wrongHttpsMethodDataProvider
     */
    public function test_using_wrong_http_method(string $httpVerb): void
    {
        $baseReadingType = $this->baseSensorReadingTypeRepository->findAll()[0];

        $this->client->request(
            $httpVerb,
            sprintf(self::DELETE_SENSOR_TRIGGER_URL, $baseReadingType->getBaseReadingTypeID()),
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
            [Request::METHOD_GET],
        ];
    }
}
