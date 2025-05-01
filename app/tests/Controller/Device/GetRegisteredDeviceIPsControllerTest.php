<?php

namespace App\Tests\Controller\Device;

use App\Entity\Common\IPLog;
use App\Repository\Common\ORM\IPLogRepository;
use App\Tests\Controller\ControllerTestCase;
use App\Tests\Traits\TestLoginTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetRegisteredDeviceIPsControllerTest extends ControllerTestCase
{
    private const GET_REGISTERED_DEVICE_IPS_URL = '/HomeApp/api/user/registered-devices';

    private IPLogRepository $ipLogRepostory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ipLogRepostory = $this->entityManager->getRepository(IPLog::class);
    }

    public function test_all_registered_devices_are_returned(): void
    {
        $this->authenticateAdminOne();
        $this->client->jsonRequest(
            Request::METHOD_GET,
            self::GET_REGISTERED_DEVICE_IPS_URL,
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $response = $this->client->getResponse();

        $responseContent = json_decode($response->getContent(), true);
        $payload = $responseContent['payload'];
        $allIPLogs = $this->ipLogRepostory->findAll();

        self::assertNotEmpty($allIPLogs);

        self::assertCount(count($allIPLogs), $payload);

        foreach ($payload as $deviceIP) {
            self::assertArrayHasKey('ipAddress', $deviceIP);
        }
    }
}
