<?php

namespace App\Tests\ESPDeviceSensor\Controller;

use App\Authentication\Controller\SecurityController;
use App\DataFixtures\Core\UserDataFixtures;
use App\DataFixtures\ESP8266\SensorFixtures;
use App\ESPDeviceSensor\Entity\ReadingTypes\Humidity;
use App\ESPDeviceSensor\Entity\ReadingTypes\Temperature;
use App\ESPDeviceSensor\Entity\SensorTypes\Dht;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class UpdateSensorBoundaryReadingsControllerTest extends WebTestCase
{
    private const UPDATE_SENSOR_BOUNDARY_READING_URL = '/HomeApp/api/user/sensors/boundary-update';

    private KernelBrowser $client;

    private ?string $userToken = null;

    private EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->setUserToken();
    }

    private function setUserToken(bool $forceToken = false): string
    {
        if ($this->userToken === null || $forceToken === true) {
//            dd('sdf');
            $this->client->request(
                Request::METHOD_POST,
                SecurityController::API_USER_LOGIN,
                [],
                [],
                ['CONTENT_TYPE' => 'application/json'],
                '{"username":"'.UserDataFixtures::ADMIN_USER.'","password":"'.UserDataFixtures::ADMIN_PASSWORD.'"}'
            );

            $requestResponse = $this->client->getResponse();
            $responseData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

            $this->userToken = $responseData['token'];

//            dd($this->userToken);
            return $responseData['token'];
        }

        return $this->userToken;
    }

    /**
     * @dataProvider multiUpdateOneCorrectOneIncorrectDataProvider
     */
    public function test_multi_update_one_correct_one_incorrect_update(string $sensorName, array $readingTypes): void
    {
        $dhtSensorRepository = $this->entityManager->getRepository(Dht::class);
        $dhtSensor = $dhtSensorRepository->findAll()[0];

        $highReading = Dht::HIGH_TEMPERATURE_READING_BOUNDARY + 1;
        $lowReading = Dht::LOW_TEMPERATURE_READING_BOUNDARY - 1;

        $currentHighReading = $dhtSensor->getTempObject()->getHighReading();
        $currentLowReading = $dhtSensor->getTempObject()->getLowReading();

        $humidHighReading = Humidity::HIGH_READING - 1;
        $humidLowReading = Humidity::LOW_READING + 1;

        $sensorData = [
            'sensorId' => $dhtSensor->getSensorObject()->getSensorNameID(),
            'sensorData' => [
                [
                    'constRecord' => true,
                    'highReading' => $humidHighReading,
                    'lowReading' => $humidLowReading,
                    'sensorType' =>  "humidity"
                ],
                [
                    'constRecord' => true,
                    'highReading' => $highReading,
                    'lowReading' => $lowReading,
                    'sensorType' =>  "temperature"
                ]
            ]
        ];

        $jsonData = json_encode($sensorData);

        $this->client->request(
            Request::METHOD_PUT,
            self::UPDATE_SENSOR_BOUNDARY_READING_URL,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken],
            $jsonData,
        );

        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

//        dd($responseData);
        $dhtSensorAfterUpdate = $dhtSensorRepository->findOneBy(['dhtID' => $dhtSensor->getSensorTypeID()]);
//dd($dhtSensorAfterUpdate, $dhtSensor->getSensorTypeID());
        self::assertNotEquals($currentHighReading, $dhtSensorAfterUpdate->getTempObject()->getHighReading());
        self::assertNotEquals($currentLowReading, $dhtSensorAfterUpdate->getTempObject()->getLowReading());

        self::assertNotEquals($highReading, (int)$dhtSensorAfterUpdate->getTempObject()->getHighReading());
        self::assertNotEquals($lowReading, (int) $dhtSensorAfterUpdate->getTempObject()->getLowReading());
//
        self::assertEquals($humidHighReading, $dhtSensorAfterUpdate->getHumidObject()->getHighReading());
        self::assertEquals($humidLowReading, $dhtSensorAfterUpdate->getHumidObject()->getLowReading());

    }

    public function multiUpdateOneCorrectOneIncorrectDataProvider(): Generator
    {
        yield [
            'sensorName' => SensorFixtures::SENSORS[Dht::NAME],
            'sensorReadingTypes' => [
                Temperature::class => [
                    'highReading' => Dht::HIGH_TEMPERATURE_READING_BOUNDARY,
                    'lowReading' => Dht::LOW_TEMPERATURE_READING_BOUNDARY,
                ],
                Humidity::class => [
                    'highReading' => Humidity::HIGH_READING + 5,
                    'lowReading' => Humidity::LOW_READING - 5,
                ]
            ]
        ];
    }
}
