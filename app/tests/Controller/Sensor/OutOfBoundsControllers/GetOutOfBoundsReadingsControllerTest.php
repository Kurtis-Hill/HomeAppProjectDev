<?php

declare(strict_types=1);

namespace App\Tests\Controller\Sensor\OutOfBoundsControllers;

use App\DTOs\Sensor\Request\OutOfBounds\GetOutOfBoundsReadingsRequestDTO;
use App\DTOs\Sensor\Response\OutOfBounds\OutOfBoundsReadingResponseDTO;
use App\Services\Sensor\OutOfBounds\Elastic\OutOfBoundsElasticSearchService;
use App\Tests\Controller\ControllerTestCase;
use Generator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetOutOfBoundsReadingsControllerTest extends ControllerTestCase
{
    private const GET_OUT_OF_BOUNDS_READINGS_URL = '/HomeApp/api/user/out-of-bounds/readings';

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockSearchService();
    }

    private function mockSearchService(array $returnData = []): void
    {
        $mockService = $this->createMock(OutOfBoundsElasticSearchService::class);
        $mockService
            ->method('search')
            ->willReturn($returnData);

        static::getContainer()->set(OutOfBoundsElasticSearchService::class, $mockService);
    }

    private function buildSampleReadings(): array
    {
        return [
            new OutOfBoundsReadingResponseDTO(
                sensorReadingID: 1,
                sensorReading: 99.5,
                createdAt: '2025-01-01T00:00:00+00:00',
                readingType: 'temperature',
            ),
            new OutOfBoundsReadingResponseDTO(
                sensorReadingID: 2,
                sensorReading: 85.0,
                createdAt: '2025-01-02T00:00:00+00:00',
                readingType: 'humidity',
            ),
        ];
    }

    // ========== Authentication Tests ==========

    public function test_unauthenticated_request_returns_401(): void
    {
        $this->client->request(
            Request::METHOD_GET,
            self::GET_OUT_OF_BOUNDS_READINGS_URL,
        );

        self::assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function test_wrong_http_methods_return_405(): void
    {
        $this->authenticateAdminOne();

        foreach ([Request::METHOD_POST, Request::METHOD_PUT, Request::METHOD_PATCH, Request::METHOD_DELETE] as $method) {
            $this->client->request(
                $method,
                self::GET_OUT_OF_BOUNDS_READINGS_URL,
            );

            self::assertResponseStatusCodeSame(
                Response::HTTP_METHOD_NOT_ALLOWED,
                sprintf('Expected 405 for %s method', $method),
            );
        }
    }

    // ========== Successful Request Tests ==========

    public function test_valid_request_no_filters_returns_200_with_empty_payload(): void
    {
        $this->authenticateAdminOne();

        $this->client->request(
            Request::METHOD_GET,
            self::GET_OUT_OF_BOUNDS_READINGS_URL,
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertArrayHasKey('payload', $responseData);
        self::assertIsArray($responseData['payload']);
    }

    public function test_valid_request_returns_200_with_populated_payload(): void
    {
        $this->mockSearchService($this->buildSampleReadings());
        $this->authenticateAdminOne();

        $this->client->request(
            Request::METHOD_GET,
            self::GET_OUT_OF_BOUNDS_READINGS_URL,
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertCount(2, $responseData['payload']);
        self::assertArrayHasKey('sensorReadingID', $responseData['payload'][0]);
        self::assertArrayHasKey('sensorReading', $responseData['payload'][0]);
        self::assertArrayHasKey('createdAt', $responseData['payload'][0]);
        self::assertArrayHasKey('readingType', $responseData['payload'][0]);
    }

    /**
     * @dataProvider validSingleReadingTypeDataProvider
     */
    public function test_valid_request_with_single_reading_type_returns_200(string $readingType): void
    {
        $this->authenticateAdminOne();

        $this->client->request(
            Request::METHOD_GET,
            self::GET_OUT_OF_BOUNDS_READINGS_URL,
            ['readingTypes' => [$readingType]],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertArrayHasKey('payload', $responseData);
    }

    public static function validSingleReadingTypeDataProvider(): Generator
    {
        yield ['temperature'];
        yield ['humidity'];
        yield ['analog'];
        yield ['latitude'];
    }

    public function test_valid_request_multiple_reading_types_returns_200(): void
    {
        $this->authenticateAdminOne();

        $this->client->request(
            Request::METHOD_GET,
            self::GET_OUT_OF_BOUNDS_READINGS_URL,
            ['readingTypes' => ['temperature', 'humidity']],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function test_valid_request_threshold_above_returns_200(): void
    {
        $this->authenticateAdminOne();

        $this->client->request(
            Request::METHOD_GET,
            self::GET_OUT_OF_BOUNDS_READINGS_URL,
            [
                'readingTypes' => ['temperature'],
                'threshold'    => 50.0,
                'direction'    => 'above',
            ],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function test_valid_request_threshold_below_returns_200(): void
    {
        $this->authenticateAdminOne();

        $this->client->request(
            Request::METHOD_GET,
            self::GET_OUT_OF_BOUNDS_READINGS_URL,
            [
                'threshold' => 10.0,
                'direction' => 'below',
            ],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function test_valid_request_date_range_returns_200(): void
    {
        $this->authenticateAdminOne();

        $this->client->request(
            Request::METHOD_GET,
            self::GET_OUT_OF_BOUNDS_READINGS_URL,
            [
                'startDate' => '2025-01-01T00:00:00+00:00',
                'endDate'   => '2025-12-31T23:59:59+00:00',
            ],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function test_valid_request_sensor_reading_id_returns_200(): void
    {
        $this->authenticateAdminOne();

        $this->client->request(
            Request::METHOD_GET,
            self::GET_OUT_OF_BOUNDS_READINGS_URL,
            ['sensorReadingID' => 1],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function test_valid_request_with_pagination_returns_200(): void
    {
        $this->authenticateAdminOne();

        $this->client->request(
            Request::METHOD_GET,
            self::GET_OUT_OF_BOUNDS_READINGS_URL,
            ['limit' => 50, 'offset' => 0],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function test_valid_request_all_reading_types_with_threshold_and_date_range(): void
    {
        $this->authenticateAdminOne();

        $this->client->request(
            Request::METHOD_GET,
            self::GET_OUT_OF_BOUNDS_READINGS_URL,
            [
                'readingTypes' => ['temperature', 'humidity', 'analog', 'latitude'],
                'threshold'    => 25.0,
                'direction'    => 'above',
                'startDate'    => '2025-01-01T00:00:00+00:00',
                'endDate'      => '2025-06-30T23:59:59+00:00',
                'limit'        => 100,
                'offset'       => 0,
            ],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    // ========== Validation Error Tests ==========

    public function test_invalid_reading_type_returns_400(): void
    {
        $this->authenticateAdminOne();

        $this->client->request(
            Request::METHOD_GET,
            self::GET_OUT_OF_BOUNDS_READINGS_URL,
            ['readingTypes' => ['pressure']],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertEquals('Validation errors occurred', $responseData['title']);
    }

    public function test_threshold_without_direction_returns_400(): void
    {
        $this->authenticateAdminOne();

        $this->client->request(
            Request::METHOD_GET,
            self::GET_OUT_OF_BOUNDS_READINGS_URL,
            ['threshold' => 50.0],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertEquals('Validation errors occurred', $responseData['title']);
        self::assertArrayHasKey('errors', $responseData);
    }

    public function test_invalid_direction_returns_400(): void
    {
        $this->authenticateAdminOne();

        $this->client->request(
            Request::METHOD_GET,
            self::GET_OUT_OF_BOUNDS_READINGS_URL,
            ['threshold' => 50.0, 'direction' => 'sideways'],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertEquals('Validation errors occurred', $responseData['title']);
    }

    public function test_start_date_without_end_date_returns_400(): void
    {
        $this->authenticateAdminOne();

        $this->client->request(
            Request::METHOD_GET,
            self::GET_OUT_OF_BOUNDS_READINGS_URL,
            ['startDate' => '2025-01-01T00:00:00+00:00'],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertEquals('Validation errors occurred', $responseData['title']);
    }

    public function test_end_date_without_start_date_returns_400(): void
    {
        $this->authenticateAdminOne();

        $this->client->request(
            Request::METHOD_GET,
            self::GET_OUT_OF_BOUNDS_READINGS_URL,
            ['endDate' => '2025-12-31T23:59:59+00:00'],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertEquals('Validation errors occurred', $responseData['title']);
    }

    public function test_limit_exceeds_maximum_returns_400(): void
    {
        $this->authenticateAdminOne();

        $this->client->request(
            Request::METHOD_GET,
            self::GET_OUT_OF_BOUNDS_READINGS_URL,
            ['limit' => GetOutOfBoundsReadingsRequestDTO::MAX_LIMIT + 1],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertEquals('Validation errors occurred', $responseData['title']);
    }

    public function test_negative_offset_returns_400(): void
    {
        $this->authenticateAdminOne();

        $this->client->request(
            Request::METHOD_GET,
            self::GET_OUT_OF_BOUNDS_READINGS_URL,
            ['offset' => -1],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertEquals('Validation errors occurred', $responseData['title']);
    }

    public function test_negative_sensor_reading_id_returns_400(): void
    {
        $this->authenticateAdminOne();

        $this->client->request(
            Request::METHOD_GET,
            self::GET_OUT_OF_BOUNDS_READINGS_URL,
            ['sensorReadingID' => -5],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);
        self::assertEquals('Validation errors occurred', $responseData['title']);
    }

    // ========== Response Structure Tests ==========

    public function test_response_payload_has_expected_keys_for_each_reading(): void
    {
        $this->mockSearchService([
            new OutOfBoundsReadingResponseDTO(
                sensorReadingID: 42,
                sensorReading: 105.7,
                createdAt: '2025-03-15T10:30:00+00:00',
                readingType: 'temperature',
            ),
        ]);

        $this->authenticateAdminOne();

        $this->client->request(
            Request::METHOD_GET,
            self::GET_OUT_OF_BOUNDS_READINGS_URL,
            ['readingTypes' => ['temperature']],
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        $responseData = json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR);

        self::assertCount(1, $responseData['payload']);
        $reading = $responseData['payload'][0];

        self::assertArrayHasKey('sensorReadingID', $reading);
        self::assertArrayHasKey('sensorReading', $reading);
        self::assertArrayHasKey('createdAt', $reading);
        self::assertArrayHasKey('readingType', $reading);

        self::assertEquals(42, $reading['sensorReadingID']);
        self::assertEquals(105.7, $reading['sensorReading']);
        self::assertEquals('2025-03-15T10:30:00+00:00', $reading['createdAt']);
        self::assertEquals('temperature', $reading['readingType']);
    }

    public function test_regular_user_can_access_endpoint(): void
    {
        $this->authenticateRegularUserOne();

        $this->client->request(
            Request::METHOD_GET,
            self::GET_OUT_OF_BOUNDS_READINGS_URL,
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
    }
}
