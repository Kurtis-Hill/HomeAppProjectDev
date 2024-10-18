<?php

namespace App\Tests\Controller\Device;

use App\Entity\Common\IPLog;
use App\Repository\Common\ORM\IPLogRepository;
use App\Tests\Traits\TestLoginTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetRegisteredDeviceIPsControllerTest extends WebTestCase
{
    use TestLoginTrait;

    private const GET_REGISTERED_DEVICE_IPS_URL = '/HomeApp/api/user/registered-devices';

    private ?string $adminToken = null;

    private ?EntityManagerInterface $entityManager;

    private KernelBrowser $client;

    private IPLogRepository $ipLogRepostory;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->adminToken = $this->setUserToken($this->client);

        $this->ipLogRepostory = $this->entityManager->getRepository(IPLog::class);
    }

    public function test_all_registered_devices_are_returned(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            self::GET_REGISTERED_DEVICE_IPS_URL,
            [],
            [],
            [
                'HTTP_AUTHORIZATION' => 'Bearer ' . $this->adminToken,
            ]
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
