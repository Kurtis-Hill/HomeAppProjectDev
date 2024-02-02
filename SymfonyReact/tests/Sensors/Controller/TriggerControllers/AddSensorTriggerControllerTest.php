<?php

namespace App\Tests\Sensors\Controller\TriggerControllers;

use App\Devices\Entity\Devices;
use App\ORM\DataFixtures\ESP8266\ESP8266DeviceFixtures;
use App\Sensors\Entity\Sensor;
use App\Tests\Traits\TestLoginTrait;
use Doctrine\ORM\EntityManagerInterface;
use JsonException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AddSensorTriggerControllerTest extends WebTestCase
{
    use TestLoginTrait;

    private const ADD_NEW_SENSOR_TRIGGER_URL = '/HomeApp/api/user/sensor-trigger/form/add';

    private ?EntityManagerInterface $entityManager;

    private KernelBrowser $client;

//    private ?Devices $device;

//    private DeviceRepository $deviceRepository;

//    private SensorRepositoryInterface $sensorRepository;

    private ?string $userToken = null;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

//        $this->sensorRepository = $this->entityManager->getRepository(Sensor::class);
//        $this->deviceRepository = $this->entityManager->getRepository(Devices::class);

        try {
            $this->device = $this->entityManager->getRepository(Devices::class)->findOneBy(['deviceName' => ESP8266DeviceFixtures::LOGIN_TEST_ACCOUNT_NAME_ADMIN_GROUP_ONE['name']]);
            $this->userToken = $this->setUserToken($this->client);
        } catch (JsonException $e) {
            error_log($e);
        }
    }
}
