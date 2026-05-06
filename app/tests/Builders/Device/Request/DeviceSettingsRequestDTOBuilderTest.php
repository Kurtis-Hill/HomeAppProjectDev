<?php

namespace App\Tests\Builders\Device\Request;

use App\Builders\Device\Request\DeviceSettingsRequestDTOBuilder;
use App\Builders\Device\Request\DeviceWifiSettingsDTOBuilder;
use App\DTOs\Device\Request\DeviceRequest\DeviceLoginCredentialsUpdateRequestDTO;
use App\DTOs\Device\Request\DeviceRequest\DeviceWifiSettingsDTO;
use App\Services\Device\Request\DeviceSettingsUpdateRequestHandler;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AttributeLoader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class DeviceSettingsRequestDTOBuilderTest extends TestCase
{
    private function buildSerializer(): Serializer
    {
        return new Serializer(
            [new ObjectNormalizer(new ClassMetadataFactory(new AttributeLoader()))],
            [new JsonEncoder()],
        );
    }

    public function test_building_device_settings_just_device_credentials_with_password(): void
    {
        $deviceSettingsRequestDTOBuilder = new DeviceSettingsRequestDTOBuilder();

        $userName = 'testUserName';
        $password = 'testPassword';
        $deviceLoginCredentialsUpdateRequestDTO = new DeviceLoginCredentialsUpdateRequestDTO($userName, $password);

        $deviceSettingsRequestDTO = $deviceSettingsRequestDTOBuilder->buildDeviceSettingsRequestDTO(
            deviceCredentials: $deviceLoginCredentialsUpdateRequestDTO,
        );

        self::assertEquals($deviceLoginCredentialsUpdateRequestDTO, $deviceSettingsRequestDTO->getDeviceCredentials());
        self::assertNull($deviceSettingsRequestDTO->getWifi());
        self::assertNull($deviceSettingsRequestDTO->getSensorData());

        $normalizedObject = $this->buildSerializer()->normalize($deviceSettingsRequestDTO, 'json', [
            'groups' => [
                DeviceSettingsRequestDTOBuilder::DEVICE_CREDENTIALS,
                DeviceSettingsUpdateRequestHandler::PASSWORD_PRESENT,
                DeviceSettingsUpdateRequestHandler::PASSWORD_NOT_PRESENT,
            ],
        ]);

        self::assertArrayNotHasKey('wifi', $normalizedObject);
        self::assertArrayNotHasKey('sensorData', $normalizedObject);
        self::assertEquals($userName, $normalizedObject['deviceCredentials']['username']);
        self::assertEquals($password, $normalizedObject['deviceCredentials']['password']);
    }

    public function test_building_device_settings_just_device_credentials_without_password(): void
    {
        $deviceSettingsRequestDTOBuilder = new DeviceSettingsRequestDTOBuilder();

        $userName = 'testUserName';
        $deviceLoginCredentialsUpdateRequestDTO = new DeviceLoginCredentialsUpdateRequestDTO($userName, null);

        $deviceSettingsRequestDTO = $deviceSettingsRequestDTOBuilder->buildDeviceSettingsRequestDTO(
            deviceCredentials: $deviceLoginCredentialsUpdateRequestDTO,
        );

        self::assertEquals($deviceLoginCredentialsUpdateRequestDTO, $deviceSettingsRequestDTO->getDeviceCredentials());
        self::assertNull($deviceSettingsRequestDTO->getWifi());
        self::assertNull($deviceSettingsRequestDTO->getSensorData());

        $normalizedObject = $this->buildSerializer()->normalize($deviceSettingsRequestDTO, 'json', [
            'groups' => [
                DeviceSettingsRequestDTOBuilder::DEVICE_CREDENTIALS,
                DeviceSettingsUpdateRequestHandler::PASSWORD_NOT_PRESENT,
            ],
        ]);

        self::assertEmpty($normalizedObject['wifi'] ?? []);
        self::assertEmpty($normalizedObject['sensorData'] ?? []);
        self::assertEquals($userName, $normalizedObject['deviceCredentials']['username']);
        self::assertArrayNotHasKey('password', $normalizedObject['deviceCredentials']);
    }

    public function test_building_device_settings_just_wifi(): void
    {
        $deviceSettingsRequestDTOBuilder = new DeviceSettingsRequestDTOBuilder();

        $ssid = 'testSsid';
        $password = 'testPassword';
        $deviceWifiSettingsDTO = new DeviceWifiSettingsDTO($ssid, $password);

        $deviceSettingsRequestDTO = $deviceSettingsRequestDTOBuilder->buildDeviceSettingsRequestDTO(
            wifi: $deviceWifiSettingsDTO,
        );

        self::assertNull($deviceSettingsRequestDTO->getDeviceCredentials());
        self::assertEquals($deviceWifiSettingsDTO, $deviceSettingsRequestDTO->getWifi());
        self::assertNull($deviceSettingsRequestDTO->getSensorData());

        $normalizedObject = $this->buildSerializer()->normalize($deviceSettingsRequestDTO, 'json', [
            'groups' => [
                DeviceSettingsRequestDTOBuilder::WIFI,
                DeviceWifiSettingsDTOBuilder::WIFI_CREDENTIALS,
            ],
        ]);

        self::assertArrayNotHasKey('deviceCredentials', $normalizedObject);
        self::assertArrayNotHasKey('sensorData', $normalizedObject);
        self::assertEquals($ssid, $normalizedObject['wifi']['ssid']);
        self::assertEquals($password, $normalizedObject['wifi']['password']);
    }

    public function test_building_device_login_credentials_and_device_settings_dto(): void
    {
        $deviceSettingsRequestDTOBuilder = new DeviceSettingsRequestDTOBuilder();

        $userName = 'testUserName';
        $password = 'testPassword';
        $deviceLoginCredentialsUpdateRequestDTO = new DeviceLoginCredentialsUpdateRequestDTO($userName, $password);

        $ssid = 'testSsid';
        $wifiPassword = 'testPassword';
        $deviceWifiSettingsDTO = new DeviceWifiSettingsDTO($ssid, $wifiPassword);

        $deviceSettingsRequestDTO = $deviceSettingsRequestDTOBuilder->buildDeviceSettingsRequestDTO(
            deviceCredentials: $deviceLoginCredentialsUpdateRequestDTO,
            wifi: $deviceWifiSettingsDTO,
        );

        self::assertEquals($deviceLoginCredentialsUpdateRequestDTO, $deviceSettingsRequestDTO->getDeviceCredentials());
        self::assertEquals($deviceWifiSettingsDTO, $deviceSettingsRequestDTO->getWifi());
        self::assertNull($deviceSettingsRequestDTO->getSensorData());

        $normalizedObject = $this->buildSerializer()->normalize($deviceSettingsRequestDTO, 'json', [
            'groups' => [
                DeviceSettingsRequestDTOBuilder::DEVICE_CREDENTIALS,
                DeviceSettingsRequestDTOBuilder::WIFI,
                DeviceSettingsUpdateRequestHandler::PASSWORD_PRESENT,
                DeviceSettingsUpdateRequestHandler::PASSWORD_NOT_PRESENT,
                DeviceWifiSettingsDTOBuilder::WIFI_CREDENTIALS,
            ],
        ]);

        self::assertArrayNotHasKey('sensorData', $normalizedObject);
        self::assertEquals($userName, $normalizedObject['deviceCredentials']['username']);
        self::assertEquals($password, $normalizedObject['deviceCredentials']['password']);
        self::assertEquals($ssid, $normalizedObject['wifi']['ssid']);
        self::assertEquals($wifiPassword, $normalizedObject['wifi']['password']);
    }
}
