<?php

namespace App\Tests\Sensors\Builders\SensorRequestBuilders;

use App\Devices\Repository\ORM\DeviceRepositoryInterface;
use App\Sensors\Builders\SensorRequestBuilders\SensorTypeDataRequestEncapsulationDTOBuilder;
use App\Sensors\DTO\Request\SendRequests\SensorDataUpdate\SingleSensorUpdateRequestDTO;
use App\Sensors\Entity\Sensor;
use App\Sensors\Entity\AbstractSensorType;
use App\Sensors\Entity\SensorTypes\Bmp;
use App\Sensors\Entity\SensorTypes\Dallas;
use App\Sensors\Entity\SensorTypes\Dht;
use App\Sensors\Entity\SensorTypes\GenericMotion;
use App\Sensors\Entity\SensorTypes\GenericRelay;
use App\Sensors\Entity\SensorTypes\LDR;
use App\Sensors\Entity\SensorTypes\Sht;
use App\Sensors\Entity\SensorTypes\Soil;
use App\Sensors\Repository\Sensors\ORM\SensorTypeRepository;
use App\Sensors\Repository\Sensors\SensorRepositoryInterface;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\ORM\EntityManagerInterface;
use Generator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class SensorTypeDataRequestEncapsulationDTOBuilderTest extends KernelTestCase
{
    private SensorTypeDataRequestEncapsulationDTOBuilder $sut;

    private ?EntityManagerInterface $entityManager;

    private SensorRepositoryInterface $sensorRepository;

    private SensorTypeRepository $sensorTypeRepository;

    private ContainerAwareInterface|Container $diContainer;

    protected function setUp(): void
    {
        self::bootKernel();

        $this->diContainer = static::getContainer();

        $this->entityManager = $this->diContainer->get('doctrine.orm.default_entity_manager');
        $this->sensorRepository = $this->diContainer->get(SensorRepositoryInterface::class);
        $this->sensorTypeRepository = $this->diContainer->get(SensorTypeRepository::class);
//        $this->sut = $this->diContainer->get(SensorTypeDataRequestEncapsulationDTOBuilder::class);
    }

    /**
     * @dataProvider oneSensorTypeDataProvider
     */
    public function test_just_adding_one_sensor_to_builder(string $sensorType): void
    {
        /** @var AbstractSensorType $sensorTypeToUser */
        $sensorTypeToUser = $this->sensorTypeRepository->findOneBy(['sensorType' => $sensorType]);

        /** @var Sensor[] $sensorsToUser */
        $sensorsToUser = $this->sensorRepository->findBy(['sensorTypeID' => $sensorTypeToUser]);

//        dd($sensorsToUser);
        $singleSensorUpdateRequestDTOs = [];
        foreach ($sensorsToUser as $sensor) {
            $singleSensorUpdateRequestDTOs[] = new SingleSensorUpdateRequestDTO(
                $sensor->getSensorName(),
                $sensor->getPinNumber(),
                $sensor->getReadingInterval(),
            );
        }

        $singleSensor = $sensorsToUser[0];
        if ($singleSensor->getSensorTypeObject()::getReadingTypeName() === GenericRelay::NAME) {
            $result = SensorTypeDataRequestEncapsulationDTOBuilder::buildSensorTypeDataRequestDTO(
                relay: $singleSensorUpdateRequestDTOs,
            );
        }
        if ($singleSensor->getSensorTypeObject()::getReadingTypeName() === GenericMotion::NAME) {
            $result = SensorTypeDataRequestEncapsulationDTOBuilder::buildSensorTypeDataRequestDTO(
                motion: $singleSensorUpdateRequestDTOs,
            );
        }
        if ($singleSensor->getSensorTypeObject()::getReadingTypeName() === Dht::NAME) {
            $result = SensorTypeDataRequestEncapsulationDTOBuilder::buildSensorTypeDataRequestDTO(
                dht: $singleSensorUpdateRequestDTOs,
            );
        }
        if ($singleSensor->getSensorTypeObject()::getReadingTypeName() === Soil::NAME) {
            $result = SensorTypeDataRequestEncapsulationDTOBuilder::buildSensorTypeDataRequestDTO(
                soil: $singleSensorUpdateRequestDTOs,
            );
        }
        if ($singleSensor->getSensorTypeObject()::getReadingTypeName() === Dallas::NAME) {
            $result = SensorTypeDataRequestEncapsulationDTOBuilder::buildSensorTypeDataRequestDTO(
                dallas: $singleSensorUpdateRequestDTOs,
            );
        }
        if ($singleSensor->getSensorTypeObject()::getReadingTypeName() === Bmp::NAME) {
            $result = SensorTypeDataRequestEncapsulationDTOBuilder::buildSensorTypeDataRequestDTO(
                bmp: $singleSensorUpdateRequestDTOs,
            );
        }
        if ($singleSensor->getSensorTypeObject()::getReadingTypeName() === LDR::NAME) {
            $result = SensorTypeDataRequestEncapsulationDTOBuilder::buildSensorTypeDataRequestDTO(
                ldr: $singleSensorUpdateRequestDTOs,
            );
        }
        if ($singleSensor->getSensorTypeObject()::getReadingTypeName() === Sht::NAME) {
            $result = SensorTypeDataRequestEncapsulationDTOBuilder::buildSensorTypeDataRequestDTO(
                sht: $singleSensorUpdateRequestDTOs,
            );
        }
        if (!isset($result)) {
            self::fail('No sensor type matched');
        }

        $context = ['groups' => $groups ?? []];

        $classMetadataFactory = new ClassMetadataFactory(
            new AnnotationLoader(
                new AnnotationReader()
            )
        );

        $encoders = [new JsonEncoder()];
        $normalizers = [new ObjectNormalizer($classMetadataFactory)];
        $serializer = new Serializer($normalizers, $encoders);

        $normalizedResponse = $serializer->normalize(
            $result,
            'json',
            $context
        );

//        dd($normalizedResponse);
        if ($singleSensor->getSensorTypeObject()::getReadingTypeName() === GenericRelay::NAME) {
            $response = $normalizedResponse['relay'];
            self::assertCount(count($singleSensorUpdateRequestDTOs), $response);
        }
        if ($singleSensor->getSensorTypeObject()::getReadingTypeName() === GenericMotion::NAME) {
            $response = $normalizedResponse['motion'];
            self::assertCount(count($singleSensorUpdateRequestDTOs), $response);
        }
        if ($singleSensor->getSensorTypeObject()::getReadingTypeName() === Dht::NAME) {
            $response = $normalizedResponse['dht'];
            self::assertCount(count($singleSensorUpdateRequestDTOs), $response);
        }
        if ($singleSensor->getSensorTypeObject()::getReadingTypeName() === Soil::NAME) {
            $response = $normalizedResponse['soil'];
            self::assertCount(count($singleSensorUpdateRequestDTOs), $response);
        }
        if ($singleSensor->getSensorTypeObject()::getReadingTypeName() === Dallas::NAME) {
            $response = $normalizedResponse['dallas'];
            self::assertCount(count($singleSensorUpdateRequestDTOs), $response);
        }
        if ($singleSensor->getSensorTypeObject()::getReadingTypeName() === Bmp::NAME) {
            $response = $normalizedResponse['bmp'];
            self::assertCount(count($singleSensorUpdateRequestDTOs), $response);
        }
        if ($singleSensor->getSensorTypeObject()::getReadingTypeName() === LDR::NAME) {
            $response = $normalizedResponse['ldr'];
            self::assertCount(count($singleSensorUpdateRequestDTOs), $response);
        }
        if ($singleSensor->getSensorTypeObject()::getReadingTypeName() === Sht::NAME) {
            $response = $normalizedResponse['sht'];
            self::assertCount(count($singleSensorUpdateRequestDTOs), $response);
        }
    }

    public function oneSensorTypeDataProvider(): Generator
    {
        yield [
            'sensorType' => GenericRelay::NAME,
        ];

        yield [
            'sensorType' => GenericMotion::NAME,
        ];

        yield [
            'sensorType' => Dht::NAME,
        ];

        yield [
            'sensorType' => Soil::NAME,
        ];

        yield [
            'sensorType' => Dallas::NAME,
        ];

        yield [
            'sensorType' => Bmp::NAME,
        ];

        yield [
            'sensorType' => LDR::NAME,
        ];

        yield [
            'sensorType' => Sht::NAME,
        ];
    }
}
