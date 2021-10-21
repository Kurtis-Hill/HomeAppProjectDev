<?php

namespace App\Tests\ESPDeviceSensor\Controller;

use App\Controller\Core\SecurityController;
use App\DataFixtures\ESP8266\ESP8266DeviceFixtures;
use App\DataFixtures\ESP8266\SensorFixtures;
use App\ESPDeviceSensor\Controller\ESPSensorUpdateController;
use App\ESPDeviceSensor\Entity\Sensors;
use App\ESPDeviceSensor\Entity\SensorType;
use App\ESPDeviceSensor\Entity\SensorTypes\Dallas;
use Generator;
use JsonException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ESPSensorUpdateControllerTest extends WebTestCase
{
    private const ESP_SENSOR_UPDATE = '/HomeApp/api/device/esp/update/current-reading';

    /**
     * @var KernelBrowser
     */
    private KernelBrowser $client;

    /**
     * @var string|null
     */
    private ?string $userToken = null;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        try {
            $this->setUserToken();
        } catch (JsonException $e) {
            error_log($e);
        }
    }

    /**
     * @return void
     * @throws JsonException
     */
    private function setUserToken(): void
    {
        if ($this->userToken === null) {
            $this->client->request(
                'POST',
                SecurityController::API_DEVICE_LOGIN,
                [],
                [],
                ['CONTENT_TYPE' => 'application/json'],
                '{"username":"'.ESP8266DeviceFixtures::ADMIN_TEST_DEVICE['referenceName'].'","password":"'.ESP8266DeviceFixtures::ADMIN_TEST_DEVICE['password'].'"}'
            );

            $requestResponse = $this->client->getResponse();
            $responseData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

            $this->userToken = $responseData['token'];
        }
    }

    /**
     * @dataProvider successfulUpdateRequestDataProvider
     * @param string $sensorType
     * @param array $sensorData
     * @throws JsonException
     */
    public function test_sending_sensor_update_requests(
        string $sensorType,
        array  $sensorData,
    ): void
    {
        $sendData['sensorType'] = $sensorType;
        $sendData['sensorData'] = [$sensorData];
        $jsonData = json_encode($sendData, JSON_THROW_ON_ERROR);

        $this->client->request(
            'POST',
            self::ESP_SENSOR_UPDATE,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
            $jsonData
        );

        $requestResponse = $this->client->getResponse();
        $responseData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertEquals(Response::HTTP_OK, $requestResponse->getStatusCode());
        self::assertEquals(ESPSensorUpdateController::SENSOR_UPDATE_SUCCESS_MESSAGE, $responseData['payload'][0]);
        self::assertEquals('Request Successful', $responseData['title']);
    }

    public function successfulUpdateRequestDataProvider(): Generator
    {
        yield [
            'sensorType' => SensorType::DHT_SENSOR,
            'sensorData' => [
                'sensorName' => SensorFixtures::SENSORS['Dht'],
                'currentReadings' => [
                    'temperatureReading' => 15.5,
                    'humidityReading' => 50
                ]
            ]
        ];
        yield [
            'sensorType' => SensorType::DALLAS_TEMPERATURE,
            'sensorData' => [
                'sensorName' => SensorFixtures::SENSORS['Dallas'],
                'currentReadings' => [
                    'temperatureReading' => 15.5,
                ]
            ]
        ];
        yield [
            'sensorType' => SensorType::BMP_SENSOR,
            'sensorData' => [
                'sensorName' => SensorFixtures::SENSORS['Bmp'],
                'currentReadings' => [
                    'temperatureReading' => 15.5,
                ]
            ]
        ];
        yield [
            'sensorType' => SensorType::SOIL_SENSOR,
            'sensorData' => [
                'sensorName' => SensorFixtures::SENSORS['Soil'],
                'currentReadings' => [
                    'temperatureReading' => 15.5,
                ]
            ]
        ];
    }

    /**
     * @dataProvider malformedSensorUpdateDataProvider
     * @throws JsonException
     */
    public function test_sending_malformed_sensor_update_request(
        string $sensorType,
        array $sensorData
    ): void
    {
        $sendData['sensorType'] = $sensorType;
        $sendData['sensorData'] = [$sensorData];
        $jsonData = json_encode($sendData, JSON_THROW_ON_ERROR);
        $this->client->request(
            'POST',
            self::ESP_SENSOR_UPDATE,
            [],
            [],
            ['HTTP_AUTHORIZATION' => 'BEARER ' . $this->userToken, 'CONTENT_TYPE' => 'application/json'],
            $jsonData
        );

        $requestResponse = $this->client->getResponse();
        $responseData = json_decode($requestResponse->getContent(), true, 512, JSON_THROW_ON_ERROR);

    }

    public function malformedSensorUpdateDataProvider(): Generator
    {
        yield [
            'sensorType' => SensorType::DHT_SENSOR,
            'sensorData' => [
                'sensorName' => SensorFixtures::SENSORS['Dht'],
                'currentReadings' => [
                    'temperatureReading' => 15.5,
                    'humidityReading' => 50
                ]
            ]
        ];
        yield [
            'sensorType' => SensorType::DALLAS_TEMPERATURE,
            'sensorData' => [
                'sensorNameWrong' => SensorFixtures::SENSORS['Dallas'],
                'currentReadings' => [
                    'temperatureReading' => 15.5,
                ]
            ]
        ];
        yield [
            'sensorType' => SensorType::BMP_SENSOR,
            'sensorData' => [
                'sensorName' => SensorFixtures::SENSORS['Bmp'],
                'currentReadingsWrong' => [
                    'temperatureReading' => 15.5,
                ]
            ]
        ];
    }
}
