<?php

namespace App\Tests\Controller\Logs;

use App\Controller\Logs\GetLogsController;
use App\Repository\Common\Elastic\LogRepository;
use App\Services\API\CommonURL;
use App\Tests\Controller\ControllerTestCase;
use Elastica\Result;
use Elastica\ResultSet;
use Generator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetLogsControllerTest extends ControllerTestCase
{
    private const GET_LOGS_URL = CommonURL::USER_BASE_API_URL . 'user/logs';

    protected function setUp(): void
    {
        parent::setUp();
        $this->client->disableReboot();
    }

    /**
     * Authenticate first (HTTP login sets the JWT on the client), then set the
     * repository mock.  With disableReboot() enabled the kernel container is
     * shared across all requests in a single test, so the mock survives from
     * after the login request through to the actual test request.
     */
    private function mockLogRepository(array $resultData = [], int $totalHits = 0): void
    {
        $mockResults = [];
        foreach ($resultData as $data) {
            $mockResult = $this->createMock(Result::class);
            $mockResult->method('getData')->willReturn($data);
            $mockResults[] = $mockResult;
        }

        $mockResultSet = $this->createMock(ResultSet::class);
        $mockResultSet->method('getTotalHits')->willReturn($totalHits);
        $mockResultSet->method('getResults')->willReturn($mockResults);

        $mockLogRepository = $this->createMock(LogRepository::class);
        $mockLogRepository->method('searchLogs')->willReturn($mockResultSet);

        static::getContainer()->set(LogRepository::class, $mockLogRepository);
    }

    public function test_regular_user_cannot_get_logs(): void
    {
        $this->authenticateRegularUserTwo();

        $this->client->request(Request::METHOD_GET, self::GET_LOGS_URL);

        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function test_unauthenticated_request_returns_unauthorized(): void
    {
        $this->client->request(Request::METHOD_GET, self::GET_LOGS_URL);

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @dataProvider invalidQueryParamsDataProvider
     */
    public function test_invalid_query_params_return_unprocessable_entity(array $queryParams): void
    {
        $this->authenticateAdminOne();

        $this->client->request(
            Request::METHOD_GET,
            self::GET_LOGS_URL,
            $queryParams,
        );

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
    }

    public static function invalidQueryParamsDataProvider(): Generator
    {
        yield 'invalid level value' => [['level' => 'INVALID_LEVEL']];
        yield 'limit too high' => [['limit' => 501]];
        yield 'limit too low' => [['limit' => 0]];
        yield 'negative offset' => [['offset' => -1]];
    }

    public function test_admin_can_get_logs_with_no_results(): void
    {
        // Auth FIRST (login request fires kernel.terminate), THEN mock.
        $this->authenticateAdminOne();
        $this->mockLogRepository([], 0);

        $this->client->request(Request::METHOD_GET, self::GET_LOGS_URL);

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        self::assertEquals(GetLogsController::REQUEST_SUCCESSFUL, $responseData['title']);
        self::assertEquals(0, $responseData['payload']['total']);
        self::assertEquals(50, $responseData['payload']['limit']);
        self::assertEquals(0, $responseData['payload']['offset']);
        self::assertEmpty($responseData['payload']['hits']);
    }

    public function test_admin_can_get_logs_with_results(): void
    {
        $logEntry = [
            'message' => 'Test log message',
            'level' => 'ERROR',
            '@timestamp' => '2026-01-01T00:00:00.000Z',
        ];

        $this->authenticateAdminOne();
        $this->mockLogRepository([$logEntry], 1);

        $this->client->request(Request::METHOD_GET, self::GET_LOGS_URL);

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        self::assertEquals(GetLogsController::REQUEST_SUCCESSFUL, $responseData['title']);
        self::assertEquals(1, $responseData['payload']['total']);
        self::assertCount(1, $responseData['payload']['hits']);
        self::assertEquals($logEntry, $responseData['payload']['hits'][0]);
    }

    public function test_admin_can_filter_logs_by_level(): void
    {
        $this->authenticateAdminOne();
        $this->mockLogRepository([['message' => 'Critical error', 'level' => 'CRITICAL']], 1);

        $this->client->request(
            Request::METHOD_GET,
            self::GET_LOGS_URL,
            ['level' => 'CRITICAL'],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        self::assertEquals(GetLogsController::REQUEST_SUCCESSFUL, $responseData['title']);
        self::assertEquals(1, $responseData['payload']['total']);
    }

    public function test_admin_can_get_logs_with_custom_limit_and_offset(): void
    {
        $this->authenticateAdminOne();
        $this->mockLogRepository([], 0);

        $this->client->request(
            Request::METHOD_GET,
            self::GET_LOGS_URL,
            ['limit' => 10, 'offset' => 5],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        self::assertEquals(10, $responseData['payload']['limit']);
        self::assertEquals(5, $responseData['payload']['offset']);
    }

    public function test_admin_can_search_logs_by_keyword(): void
    {
        $logEntry = ['message' => 'Connection timeout', 'level' => 'ERROR'];

        $this->authenticateAdminOne();
        $this->mockLogRepository([$logEntry], 1);

        $this->client->request(
            Request::METHOD_GET,
            self::GET_LOGS_URL,
            ['keyword' => 'timeout'],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        self::assertEquals(GetLogsController::REQUEST_SUCCESSFUL, $responseData['title']);
        self::assertEquals(1, $responseData['payload']['total']);
        self::assertEquals($logEntry, $responseData['payload']['hits'][0]);
    }

    public function test_admin_can_search_logs_by_date_range(): void
    {
        $logEntry = ['message' => 'Date range log', 'level' => 'INFO'];

        $this->authenticateAdminOne();
        $this->mockLogRepository([$logEntry], 1);

        $this->client->request(
            Request::METHOD_GET,
            self::GET_LOGS_URL,
            [
                'startDate' => '2026-01-01T00:00:00',
                'endDate'   => '2026-12-31T23:59:59',
            ],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        self::assertEquals(GetLogsController::REQUEST_SUCCESSFUL, $responseData['title']);
        self::assertEquals(1, $responseData['payload']['total']);
    }
}
