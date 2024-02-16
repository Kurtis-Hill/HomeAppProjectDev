<?php

namespace App\Tests\Sensors\Controller\TriggerControllers;

use App\Devices\Entity\Devices;
use App\Devices\Repository\ORM\DeviceRepository;
use App\ORM\DataFixtures\Core\UserDataFixtures;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Relay;
use App\Sensors\Entity\Sensor;
use App\Sensors\Repository\ReadingType\ORM\RelayRepository;
use App\Sensors\Repository\Sensors\ORM\SensorRepository;
use App\Tests\Traits\TestLoginTrait;
use App\User\Entity\Group;
use App\User\Entity\User;
use App\User\Repository\ORM\GroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use JsonException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class GetSensorTriggerControllerTest extends WebTestCase
{
    use TestLoginTrait;

    private const GET_SENSOR_TRIGGER_URL = '/HomeApp/api/user/sensor-trigger/get/all';

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

    public function test_getting_all_the_triggers_for_user(): void
    {
        $user = $this->entityManager->getRepository(User::class)->findOneBy(['email' => UserDataFixtures::REGULAR_USER_EMAIL_ONE]);
        $groupsUserIsApartOf = $this->groupRepository->findGroupsUserIsApartOf($user);
        $groupsUserIsNotApartOf = $this->groupRepository->findGroupsUserIsNotApartOf($user);

        $devicesUserIsApartOf = $this->deviceRepository->findBy(['groupID' => $groupsUserIsApartOf]);
        $devicesUserIsNotApartOf = $this->deviceRepository->findBy(['groupID' => $groupsUserIsNotApartOf]);

        $sensorsUserIsApartOf = $this->sensorRepository->findBy(['deviceID' => $devicesUserIsApartOf]);
        $sensorsUserIsNotApartOf = $this->sensorRepository->findBy(['deviceID' => $devicesUserIsNotApartOf]);

        $userToken = $this->setUserToken($this->client, $user->getEmail(), UserDataFixtures::REGULAR_PASSWORD);

        $this->client->request(
            Request::METHOD_GET,
            self::GET_SENSOR_TRIGGER_URL,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'Bearer ' . $userToken]
        );

        self::assertResponseIsSuccessful();

        $response = json_decode($this->client->getResponse()->getContent(), true);

        dd($response);
    }

    public function test_admin_gets_all_triggers(): void
    {

    }

}
