<?php

namespace App\Tests\Builders\Device\Request;

use App\Builders\Device\Request\DeviceSettingsRequestDTOBuilder;
use App\Builders\Device\Request\DeviceWifiSettingsDTOBuilder;
use App\DTOs\Device\Request\DeviceRequest\DeviceLoginCredentialsUpdateRequestDTO;
use App\DTOs\Device\Request\DeviceRequest\DeviceWifiSettingsDTO;
use App\Services\Device\Request\DeviceSettingsUpdateRequestHandler;
use Doctrine\Common\Annotations\AnnotationReader;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class DeviceSettingsRequestDTOBuilderTest extends TestCase
{
    public function test_building_device_settings_just_device_credentials_with_password(): void
    {
        $deviceSettingsRequestDTOBuilder = new DeviceSettingsRequestDTOBuilder();

        $userName = 'testUserName';
        $password = 'testPassword';
        $deviceLoginCredentialsUpdateRequestDTO = new DeviceLoginCredentialsUpdateRequestDTO(
            $userName,
            $password,
        );

        $deviceSettingsRequestDTO = $deviceSettingsRequestDTOBuilder->buildDeviceSettingsRequestDTO(
            deviceCredentials: $deviceLoginCredentialsUpdateRequestDTO,
        );

        self::assertEquals(
            $deviceLoginCredentialsUpdateRequestDTO,
            $deviceSettingsRequestDTO->getDeviceCredentials(),
        );
        self::assertNull($deviceSettingsRequestDTO->getWifi());
        self::assertNull($deviceSettingsRequestDTO->getSensorData());

        $encoders = [new JsonEncoder()];

        $annotationClassMetadataFactory = new ClassMetadataFactory(
            new AnnotationLoader(
                new AnnotationReader()
            )
        );
        $normalizers = [new ObjectNormalizer($annotationClassMetadataFactory)];
        $serializer = new Serializer($normalizers, $encoders);

        $normalizedObject = $serializer->normalize($deviceSettingsRequestDTO, 'json', [
            'groups' => [
                DeviceSettingsRequestDTOBuilder::DEVICE_CREDENTIALS,
                DeviceSettingsUpdateRequestHandler::PASSWORD_PRESENT,
                DeviceSettingsUpdateRequestHandler::PASSWORD_NOT_PRESENT
            ],
        ]);

        self::assertArrayNotHasKey('wifi', $normalizedObject);
        self::assertArrayNotHasKey('sensorData', $normalizedObject);

        $deviceCredentials = $normalizedObject['deviceCredentials'];

        self::assertEquals($userName, $deviceCredentials['username']);
        self::assertEquals($password, $deviceCredentials['password']);
    }

    public function test_building_device_settings_just_device_credentials_without_password(): void
    {
        $deviceSettingsRequestDTOBuilder = new \App\Builders\Device\Request\DeviceSettingsRequestDTOBuilder();

        $userName = 'testUserName';
        $deviceLoginCredentialsUpdateRequestDTO = new DeviceLoginCredentialsUpdateRequestDTO(
            $userName,
            null,
        );

        $deviceSettingsRequestDTO = $deviceSettingsRequestDTOBuilder->buildDeviceSettingsRequestDTO(
            deviceCredentials: $deviceLoginCredentialsUpdateRequestDTO,
        );

        self::assertEquals(
            $deviceLoginCredentialsUpdateRequestDTO,
            $deviceSettingsRequestDTO->getDeviceCredentials(),
        );
        self::assertNull($deviceSettingsRequestDTO->getWifi());
        self::assertNull($deviceSettingsRequestDTO->getSensorData());

        $encoders = [new JsonEncoder()];

        $annotationClassMetadataFactory = new ClassMetadataFactory(
            new AnnotationLoader(
                new AnnotationReader()
            )
        );
        $normalizers = [new ObjectNormalizer($annotationClassMetadataFactory)];
        $serializer = new Serializer($normalizers, $encoders);

        $normalizedObject = $serializer->normalize($deviceSettingsRequestDTO, 'json', [
            'groups' => [
                \App\Builders\Device\Request\DeviceSettingsRequestDTOBuilder::DEVICE_CREDENTIALS,
                DeviceSettingsUpdateRequestHandler::PASSWORD_NOT_PRESENT,
            ],
        ]);

        self::assertEmpty($normalizedObject['wifi'] ?? []);
        self::assertEmpty($normalizedObject['sensorData'] ?? []);

        $deviceCredentials = $normalizedObject['deviceCredentials'];

        self::assertEquals($userName, $deviceCredentials['username']);
        self::assertArrayNotHasKey('password', $deviceCredentials);
    }

    public function test_building_device_settings_just_wifi(): void
    {
        $deviceSettingsRequestDTOBuilder = new DeviceSettingsRequestDTOBuilder();

        $ssid = 'testSsid';
        $password = 'testPassword';
        $deviceWifiSettingsDTO = new DeviceWifiSettingsDTO(
            $ssid,
            $password,
        );

        $deviceSettingsRequestDTO = $deviceSettingsRequestDTOBuilder->buildDeviceSettingsRequestDTO(
            wifi: $deviceWifiSettingsDTO,
        );

        self::assertNull($deviceSettingsRequestDTO->getDeviceCredentials());
        self::assertEquals($deviceWifiSettingsDTO, $deviceSettingsRequestDTO->getWifi());
        self::assertNull($deviceSettingsRequestDTO->getSensorData());

        $encoders = [new JsonEncoder()];

        $annotationClassMetadataFactory = new ClassMetadataFactory(
            new AnnotationLoader(
                new AnnotationReader()
            )
        );
        $normalizers = [new ObjectNormalizer($annotationClassMetadataFactory)];
        $serializer = new Serializer($normalizers, $encoders);

        $normalizedObject = $serializer->normalize($deviceSettingsRequestDTO, 'json', [
            'groups' => [
                \App\Builders\Device\Request\DeviceSettingsRequestDTOBuilder::WIFI,
                \App\Builders\Device\Request\DeviceWifiSettingsDTOBuilder::WIFI_CREDENTIALS,
            ],
        ]);

        self::assertArrayNotHasKey('deviceCredentials', $normalizedObject);
        self::assertArrayNotHasKey('sensorData', $normalizedObject);

        $wifi = $normalizedObject['wifi'];

        self::assertEquals($ssid, $wifi['ssid']);
        self::assertEquals($password, $wifi['password']);
    }

    public function test_building_device_login_credentials_and_device_settings_dto(): void
    {
        $deviceSettingsRequestDTOBuilder = new DeviceSettingsRequestDTOBuilder();

        $userName = 'testUserName';
        $password = 'testPassword';
        $deviceLoginCredentialsUpdateRequestDTO = new DeviceLoginCredentialsUpdateRequestDTO(
            $userName,
            $password,
        );

        $ssid = 'testSsid';
        $password = 'testPassword';
        $deviceWifiSettingsDTO = new DeviceWifiSettingsDTO(
            $ssid,
            $password,
        );

        $deviceSettingsRequestDTO = $deviceSettingsRequestDTOBuilder->buildDeviceSettingsRequestDTO(
            deviceCredentials: $deviceLoginCredentialsUpdateRequestDTO,
            wifi: $deviceWifiSettingsDTO,
        );

        self::assertEquals(
            $deviceLoginCredentialsUpdateRequestDTO,
            $deviceSettingsRequestDTO->getDeviceCredentials(),
        );
        self::assertEquals($deviceWifiSettingsDTO, $deviceSettingsRequestDTO->getWifi());
        self::assertNull($deviceSettingsRequestDTO->getSensorData());

        $encoders = [new JsonEncoder()];

        $annotationClassMetadataFactory = new ClassMetadataFactory(
            new AnnotationLoader(
                new AnnotationReader()
            )
        );
        $normalizers = [new ObjectNormalizer($annotationClassMetadataFactory)];
        $serializer = new Serializer($normalizers, $encoders);

        $normalizedObject = $serializer->normalize($deviceSettingsRequestDTO, 'json', [
            'groups' => [
                DeviceSettingsRequestDTOBuilder::DEVICE_CREDENTIALS,
                \App\Builders\Device\Request\DeviceSettingsRequestDTOBuilder::WIFI,
                \App\Services\Device\Request\DeviceSettingsUpdateRequestHandler::PASSWORD_PRESENT,
                DeviceSettingsUpdateRequestHandler::PASSWORD_NOT_PRESENT,
                DeviceWifiSettingsDTOBuilder::WIFI_CREDENTIALS,
            ],
        ]);

        self::assertArrayNotHasKey('sensorData', $normalizedObject);

        $deviceCredentials = $normalizedObject['deviceCredentials'];

        self::assertEquals($userName, $deviceCredentials['username']);
        self::assertEquals($password, $deviceCredentials['password']);

        $wifi = $normalizedObject['wifi'];

        self::assertEquals($ssid, $wifi['ssid']);
        self::assertEquals($password, $wifi['password']);
    }
}
