<?php

namespace App\Tests\Builders\Sensor\SensorRequestBuilders;

use App\Builders\Sensor\Request\SensorRequestBuilders\SensorTypeDataRequestEncapsulationDTOBuilder;
use App\DTOs\Sensor\Request\SendRequests\SensorDataUpdate\SingleSensorUpdateRequestDTO;
use App\Entity\Sensor\AbstractSensorType;
use App\Entity\Sensor\SensorTypes\Bmp;
use App\Entity\Sensor\SensorTypes\Dallas;
use App\Entity\Sensor\SensorTypes\Dht;
use App\Entity\Sensor\SensorTypes\GenericMotion;
use App\Entity\Sensor\SensorTypes\GenericRelay;
use App\Entity\Sensor\SensorTypes\LDR;
use App\Entity\Sensor\SensorTypes\Sht;
use App\Entity\Sensor\SensorTypes\Soil;
use App\Repository\Sensor\Sensors\ORM\SensorTypeRepository;
use App\Repository\Sensor\Sensors\SensorRepositoryInterface;
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

//        $this->entityManager = $this->diContainer->get('doctrine.orm.default_entity_manager');
        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
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
        $sensorTypeRepository = $this->entityManager->getRepository($sensorType);
        $sensorTypeObject = $sensorTypeRepository->findAll()[0];
        /** @var \App\Entity\Sensor\Sensor[] $sensorsToUser */
        $sensorsToUser = $this->sensorRepository->findBy(['sensorTypeID' => $sensorTypeObject]);

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
            $result = \App\Builders\Sensor\Request\SensorRequestBuilders\SensorTypeDataRequestEncapsulationDTOBuilder::buildSensorTypeDataRequestDTO(
                bmp: $singleSensorUpdateRequestDTOs,
            );
        }
        if ($singleSensor->getSensorTypeObject()::getReadingTypeName() === LDR::NAME) {
            $result = \App\Builders\Sensor\Request\SensorRequestBuilders\SensorTypeDataRequestEncapsulationDTOBuilder::buildSensorTypeDataRequestDTO(
                ldr: $singleSensorUpdateRequestDTOs,
            );
        }
        if ($singleSensor->getSensorTypeObject()::getReadingTypeName() === Sht::NAME) {
            $result = \App\Builders\Sensor\Request\SensorRequestBuilders\SensorTypeDataRequestEncapsulationDTOBuilder::buildSensorTypeDataRequestDTO(
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
            'sensorType' => GenericRelay::class,
        ];

        yield [
            'sensorType' => GenericMotion::class,
        ];

        yield [
            'sensorType' => Dht::class,
        ];

        yield [
            'sensorType' => Soil::class,
        ];

        yield [
            'sensorType' => Dallas::class,
        ];

        yield [
            'sensorType' => Bmp::class,
        ];

        yield [
            'sensorType' => LDR::class,
        ];

        yield [
            'sensorType' => Sht::class,
        ];
    }
}
