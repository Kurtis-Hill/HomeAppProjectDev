<?php

namespace Sensors\SensorDataServices\SensorReadingUpdate\OutOfBounds;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class SensorReadingTypeOutOfBoundsServiceTest extends KernelTestCase
{
    public function test_const_record_saves_out_of_range_high_readings_analog(string $sensorName, string $sensorClass): void
    {
        $sensor = $this->entityManager->getRepository(Sensor::class)->findOneBy(['sensorName' => SensorFixtures::SENSORS[$sensorName]]);
        $soilSensor = $this->entityManager->getRepository($sensorClass)->findOneBy(['sensorNameID' => $sensor->getSensorNameID()]);

        $analogSensor = $soilSensor->getAnalogObject();

        $highReading = $analogSensor->getHighReading();
        $analogSensor->setCurrentReading($highReading + 5);

        $this->sut->checkAndProcessConstRecord($analogSensor);
        $this->entityManager->flush();

        $constRecord = $this->entityManager->getRepository(ConstAnalog::class);
        $constRecordings = $constRecord->findBy(['sensorReadingTypeID' => $analogSensor->getSensorID()]);

        $constRecordings = array_pop($constRecordings);
        self::assertNotEmpty($constRecordings);
        self::assertEquals($analogSensor->getCurrentReading(), $constRecordings->getSensorReading());
    }
}
