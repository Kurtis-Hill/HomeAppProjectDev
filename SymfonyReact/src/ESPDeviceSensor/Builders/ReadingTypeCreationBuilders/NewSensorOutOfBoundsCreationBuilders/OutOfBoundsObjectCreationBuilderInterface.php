<?php

namespace App\ESPDeviceSensor\Builders\ReadingTypeCreationBuilders\NewSensorOutOfBoundsCreationBuilders;

use App\ESPDeviceSensor\Entity\OutOfRangeRecordings\OutOfBoundsEntityInterface;

interface OutOfBoundsObjectCreationBuilderInterface
{
    public function buildOutOfBoundsObject(): OutOfBoundsEntityInterface;
}
