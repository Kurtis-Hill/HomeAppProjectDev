<?php

namespace App\Tests\Traits;

use App\Authentication\Controller\SecurityController;
use App\Doctrine\DataFixtures\Core\UserDataFixtures;
use App\Doctrine\DataFixtures\ESP8266\ESP8266DeviceFixtures;
use JsonException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Request;

trait TestLoginTrait
{
    /**
     * @throws JsonException
     */
    private function setUserToken(
        KernelBrowser $client,
        ?string $username = null,
        ?string $password = null,
    ): ?string {
        $username = $username ?? UserDataFixtures::ADMIN_USER;
        $password = $password ?? UserDataFixtures::ADMIN_PASSWORD;

        $client->request(
            Request::METHOD_POST,
            SecurityController::API_USER_LOGIN,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"username":"'. $username .'","password":"'. $password .'"}'
        );

        $requestResponse = $client->getResponse();

        try {
            $responseData = json_decode(
                $requestResponse->getContent(),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (JsonException) {
            throw new JsonException('Failed to (json)decode user/device login token request');
        }

        return $responseData['token'];
    }

    public function setDeviceToken(
        KernelBrowser $client,
        ?string $username = ESP8266DeviceFixtures::ADMIN_TEST_DEVICE['referenceName'],
        ?string $password = ESP8266DeviceFixtures::ADMIN_TEST_DEVICE['password'],
    ) {
        $client->request(
            Request::METHOD_POST,
            SecurityController::API_DEVICE_LOGIN,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            '{"username":"'. $username .'","password":"'. $password .'"}'
        );

        $requestResponse = $client->getResponse();

        try {
            $responseData = json_decode(
                $requestResponse->getContent(),
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (JsonException) {
            throw new JsonException('Failed to (json)decode user/device login token request');
        }

        return $responseData['token'];
    }
}
