<?php

namespace App\Factories\Sensor\ReadingTypeFactories;

use App\Builders\Sensor\Response\ReadingTypeResponseBuilders\BoolReadingTypeResponseBuilder;
use App\Builders\Sensor\Response\ReadingTypeResponseBuilders\ReadingTypeResponseBuilderInterface;
use App\Builders\Sensor\Response\ReadingTypeResponseBuilders\StandardReadingTypeResponseBuilder;
use App\Entity\Sensor\ReadingTypes\BoolReadingTypes\BoolReadingSensorInterface;
use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\StandardReadingSensorInterface;
use App\Entity\Sensor\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Exceptions\Sensor\ReadingTypeNotSupportedException;

class ReadingTypeResponseBuilderFactory
{
    private StandardReadingTypeResponseBuilder $standardReadingTypeResponseBuilder;

    private BoolReadingTypeResponseBuilder $boolReadingTypeResponseBuilder;

    public function __construct(
        StandardReadingTypeResponseBuilder $standardReadingTypeResponseBuilder,
        BoolReadingTypeResponseBuilder $boolReadingTypeResponseBuilder
    ) {
        $this->standardReadingTypeResponseBuilder = $standardReadingTypeResponseBuilder;
        $this->boolReadingTypeResponseBuilder = $boolReadingTypeResponseBuilder;
    }

    /**
     * @throws ReadingTypeNotSupportedException
     */
    public function getStandardReadingTypeResponseBuilder(AllSensorReadingTypeInterface $readingType): ReadingTypeResponseBuilderInterface
    {
        if ($readingType instanceof StandardReadingSensorInterface) {
            return $this->standardReadingTypeResponseBuilder;
        }
        if ($readingType instanceof BoolReadingSensorInterface) {
            return $this->boolReadingTypeResponseBuilder;
        }

        throw new ReadingTypeNotSupportedException(
            sprintf(
                ReadingTypeNotSupportedException::READING_TYPE_NOT_SUPPORTED_FOR_THIS_SENSOR_MESSAGE,
                $readingType->getReadingType(),
            )
        );
    }
}
