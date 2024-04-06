<?php

namespace App\Tests\Sensors\Builders\RequestSensorCurrentReadingUpdateArgumentBuilder;

use App\Sensors\Builders\Request\RequestSensorCurrentReadingUpdateArgumentBuilder\RequestSensorCurrentReadingUpdateArgumentBuilder;
use App\Sensors\DTO\Internal\CurrentReadingDTO\BoolCurrentReadingUpdateDTO;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\Relay;
use App\Sensors\Entity\Sensor;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class RequestSensorCurrentReadingUpdateArgumentBuilderTest extends TestCase
{
    private RequestSensorCurrentReadingUpdateArgumentBuilder $sut;

    protected function setUp(): void
    {
        $this->sut = new \App\Sensors\Builders\Request\RequestSensorCurrentReadingUpdateArgumentBuilder\RequestSensorCurrentReadingUpdateArgumentBuilder();
        parent::setUp();
    }
    public function test_building_dto_correctly(): void
    {
        $sensor = new Sensor();
        $sensorName = 'test-sensorname';
        $sensor->setSensorName($sensorName);
        $sensor->setPinNumber(50);

        $boolCurrentReadingUpdateDTO = new BoolCurrentReadingUpdateDTO(
            Relay::READING_TYPE,
            true,
        );

        $dto = $this->sut->buildSensorRequestArguments(
            $sensor,
            $boolCurrentReadingUpdateDTO,
        );

        self::assertEquals(
            $sensorName,
            $dto->getSensorName(),
        );

        self::assertTrue(
            $dto->getRequestedReading()
        );
    }

    public function test_serlizing_correct_dto_for_transport(): void
    {
        $sensor = new Sensor();
        $sensorName = 'test-sensorname';
        $sensor->setSensorName($sensorName);
        $sensor->setPinNumber(1);

        $boolCurrentReadingUpdateDTO = new BoolCurrentReadingUpdateDTO(
            Relay::READING_TYPE,
            true,
        );

        $dto = $this->sut->buildSensorRequestArguments(
            $sensor,
            $boolCurrentReadingUpdateDTO,
        );

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer()];
        $serializer = new Serializer($normalizers, $encoders);

        $serializedDto = $serializer->serialize(
            $dto,
            'json',
        );

        $expectedJson = '{
            "sensorName": "test-sensorname",
            "pinNumber": 1,
            "requestedReading": true
        }';

        self::assertJsonStringEqualsJsonString(
            $expectedJson,
            $serializedDto,
        );
    }
}
