<?php

namespace App\Tests\Devices\Controller;

use App\Common\Entity\IPLog;
use App\Common\Repository\IPLogRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RegisterNewDeviceControllerTest extends WebTestCase
{
    private const REGISTER_NEW_DEVICE_URL = 'HomeApp/api/device/register';

    private KernelBrowser $client;

    private ?EntityManagerInterface $entityManager;

    private IPLogRepository $ipLogRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        $this->ipLogRepository = $this->entityManager->getRepository(IPLog::class);
    }

    protected function tearDown(): void
    {
        $this->entityManager->close();
        $this->entityManager = null;
        parent::tearDown();
    }



    public function test_correct_request_returns_success(): void
    {
        $ipAddress = '192.168.115';

        $this->client->request(
            Request::METHOD_POST,
            self::REGISTER_NEW_DEVICE_URL,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode(['ipAddress' => $ipAddress])
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $ipLog = $this->ipLogRepository->findOneBy(['ipAddress' => $ipAddress]);
        self::assertNotNull($ipLog);

        self::assertEquals($ipAddress, $ipLog->getIpAddress());
    }


}
