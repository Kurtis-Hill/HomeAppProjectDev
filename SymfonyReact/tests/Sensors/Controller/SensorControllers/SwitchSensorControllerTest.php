<?php

namespace App\Tests\Sensors\Controller\SensorControllers;

use App\ORM\DataFixtures\Core\UserDataFixtures;
use App\ORM\DataFixtures\ESP8266\SensorFixtures;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\BoolReadingSensorInterface;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Relay;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\SensorTypes\GenericRelay;
use App\Sensors\Repository\Sensors\SensorRepositoryInterface;
use App\Tests\Traits\TestLoginTrait;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class SwitchSensorControllerTest extends WebTestCase
{
    use TestLoginTrait;

    private const SWITCH_CONTROLLER = '/HomeApp/api/device/switch-sensor';

    private KernelBrowser $client;

    private ?EntityManagerInterface $entityManager;

    private SensorRepositoryInterface $sensorRepository;

    private ?string $adminToken = null;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->sensorRepository = $this->entityManager->getRepository(Sensor::class);

        $this->adminToken = $this->setUserToken(
            $this->client,
            UserDataFixtures::ADMIN_USER_EMAIL_ONE,
            UserDataFixtures::ADMIN_PASSWORD,
        );
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
        parent::tearDown();
    }

    /**
     * @dataProvider successfulSwitchDataProvider
     */
    public function test_sending_successful_switch_sensor_request(
        string $sensorType,
        string $sensorName,
        array $currentReadings,
    ): void {
        /** @var Sensor $sensor */
        $sensor = $this->sensorRepository->findOneBy(['sensorName' => $sensorName]);

//        dd($sensor, $sensorName);
        $sensorReadingTypeRepository = $this->entityManager
            ->getRepository($sensorType);

//        dd($sensorReadingTypeRepository);
        /** @var BoolReadingSensorInterface $sensorReadingType */
        $sensorReadingType = $sensorReadingTypeRepository
            ->findOneBy(['sensor' => $sensor->getSensorID()]);

        $requestData = [
            'sensorData' => [
                [

                    'sensorName' => $sensorName,
                    'currentReadings' => $currentReadings
                ],
            ],
        ];
        $this->client->request(
            Request::METHOD_POST,
            self::SWITCH_CONTROLLER,
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $this->adminToken,
                'CONTENT_TYPE' => 'application/json',
            ],
            json_encode($requestData)
        );
//        dd($this->client->getResponse()->getContent());

        $this->assertResponseIsSuccessful();
    }

    public function successfulSwitchDataProvider(): Generator
    {
        yield [
            'sensorType' => GenericRelay::class,
            'sensorName' => SensorFixtures::RELAY_SENSOR_NAME,
            'currentReadings' => [
                Relay::READING_TYPE => false
            ]
        ];
    }
}
