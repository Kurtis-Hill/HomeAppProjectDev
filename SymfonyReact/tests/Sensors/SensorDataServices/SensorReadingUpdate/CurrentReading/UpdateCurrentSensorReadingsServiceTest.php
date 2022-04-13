<?php

namespace Sensors\SensorDataServices\SensorReadingUpdate\CurrentReading;

use App\Sensors\SensorDataServices\SensorReadingUpdate\CurrentReading\UpdateCurrentSensorReadingsService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class UpdateCurrentSensorReadingsServiceTest extends KernelTestCase
{
    private UpdateCurrentSensorReadingsService $sut;

    protected function setUp(): void
    {
        self::bootKernel();

        $container = static::getContainer();
        $this->sut = $container->get(UpdateCurrentSensorReadingsService::class);
    }


}
