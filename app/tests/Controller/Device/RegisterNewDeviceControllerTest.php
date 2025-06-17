<?php

namespace App\Tests\Controller\Device;

use App\Entity\Common\IPLog;
use App\Repository\Common\ORM\IPLogRepository;
use App\Tests\Controller\ControllerTestCase;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RegisterNewDeviceControllerTest extends ControllerTestCase
{
    private const REGISTER_NEW_DEVICE_URL = 'HomeApp/api/device/register';

    private IPLogRepository $ipLogRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ipLogRepository = $this->entityManager->getRepository(IPLog::class);
    }

    public function test_correct_request_returns_success(): void
    {
        $ipAddress = '192.168.115';

        $this->client->jsonRequest(
            Request::METHOD_POST,
            self::REGISTER_NEW_DEVICE_URL,
            ['ipAddress' => $ipAddress]
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $ipLog = $this->ipLogRepository->findOneBy(['ipAddress' => $ipAddress]);
        self::assertNotNull($ipLog);

        self::assertEquals($ipAddress, $ipLog->getIpAddress());
    }

    /**
     * @dataProvider wrongDataTypesDataProvider
     */
    public function test_wrong_data_types_returns_error(mixed $ipaddress): void
    {
        $this->client->jsonRequest(
            Request::METHOD_POST,
            self::REGISTER_NEW_DEVICE_URL,
            ['ipAddress' => $ipaddress]
        );

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public function wrongDataTypesDataProvider(): Generator
    {
        yield [
            'ipAddress' => 123,
        ];

        yield [
            'ipAddress' => true,
        ];

        yield [
            'ipAddress' => [],
        ];

        yield [
            'ipAddress' => null,
        ];
    }
}
