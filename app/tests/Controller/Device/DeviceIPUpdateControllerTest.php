<?php
declare(strict_types=1);

namespace App\Tests\Controller\Device;

use App\DataFixtures\ESP8266\ESP8266DeviceFixtures;
use App\Entity\Device\Devices;
use App\Repository\Device\ORM\DeviceRepositoryInterface;
use App\Services\API\HTTPStatusCodes;
use App\Tests\Controller\ControllerTestCase;
use Generator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DeviceIPUpdateControllerTest extends ControllerTestCase
{
    private const DEVICE_IP_UPDATE_URL = '/HomeApp/api/device/ipupdate';

    private DeviceRepositoryInterface $deviceRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->deviceRepository = $this->entityManager->getRepository(Devices::class);
    }

    public function test_device_can_update_its_own_ip_address(): void
    {
        $this->authenticateTestDevice();

        $device = $this->deviceRepository->findOneBy(
            ['deviceName' => ESP8266DeviceFixtures::ADMIN_TEST_DEVICE['referenceName']]
        );

        self::assertNotNull($device);
        $deviceId = $device->getDeviceID();

        $newIpAddress = '192.168.1.200';
        $this->client->jsonRequest(
            Request::METHOD_PUT,
            self::DEVICE_IP_UPDATE_URL,
            ['ipAddress' => $newIpAddress]
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertEquals('Device IP address updated successfully', $responseData['title']);

        // Re-fetch from DB to assert the IP was actually persisted
        $updatedDevice = $this->deviceRepository->findOneById($deviceId);
        self::assertEquals($newIpAddress, $updatedDevice->getIpAddress());
    }

    public function test_device_can_update_ip_address_via_post(): void
    {
        $this->authenticateTestDevice();

        $device = $this->deviceRepository->findOneBy(
            ['deviceName' => ESP8266DeviceFixtures::ADMIN_TEST_DEVICE['referenceName']]
        );
        self::assertNotNull($device);
        $deviceId = $device->getDeviceID();

        $newIpAddress = '10.0.0.55';
        $this->client->jsonRequest(
            Request::METHOD_POST,
            self::DEVICE_IP_UPDATE_URL,
            ['ipAddress' => $newIpAddress]
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $updatedDevice = $this->deviceRepository->findOneById($deviceId);
        self::assertEquals($newIpAddress, $updatedDevice->getIpAddress());
    }

    public function test_unauthenticated_request_is_rejected(): void
    {
        $this->client->jsonRequest(
            Request::METHOD_PUT,
            self::DEVICE_IP_UPDATE_URL,
            ['ipAddress' => '192.168.1.1']
        );

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function test_regular_user_cannot_update_device_ip(): void
    {
        $this->authenticateAdminOne();

        $this->client->jsonRequest(
            Request::METHOD_PUT,
            self::DEVICE_IP_UPDATE_URL,
            ['ipAddress' => '192.168.1.1']
        );

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function test_missing_ip_address_returns_bad_request(): void
    {
        $this->authenticateTestDevice();

        $this->client->jsonRequest(
            Request::METHOD_PUT,
            self::DEVICE_IP_UPDATE_URL,
            []
        );

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertArrayHasKey('errors', $responseData);
    }

    /**
     * @dataProvider invalidIpAddressDataProvider
     */
    public function test_invalid_ip_address_returns_bad_request(mixed $ipAddress): void
    {
        $this->authenticateTestDevice();

        $this->client->jsonRequest(
            Request::METHOD_PUT,
            self::DEVICE_IP_UPDATE_URL,
            ['ipAddress' => $ipAddress]
        );

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);

        $responseData = json_decode(
            $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR
        );

        self::assertArrayHasKey('errors', $responseData);
    }

    public static function invalidIpAddressDataProvider(): Generator
    {
        yield 'not an ip address' => [
            'ipAddress' => 'not-an-ip',
        ];

        yield 'empty string' => [
            'ipAddress' => '',
        ];

        yield 'partial ip' => [
            'ipAddress' => '192.168',
        ];

        yield 'out of range octets' => [
            'ipAddress' => '999.999.999.999',
        ];
    }

    public function test_sending_malformed_json_returns_bad_request(): void
    {
        $this->authenticateTestDevice();

        $this->client->request(
            Request::METHOD_PUT,
            self::DEVICE_IP_UPDATE_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            'this is not json'
        );

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }
}
