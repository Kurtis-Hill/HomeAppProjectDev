<?php

namespace App\Sensors\Factories\ReadingTypeFactories;

use App\Sensors\Builders\ReadingTypeResponseBuilders\BoolReadingTypeResponseBuilder;
use App\Sensors\Builders\ReadingTypeResponseBuilders\ReadingTypeResponseBuilderInterface;
use App\Sensors\Builders\ReadingTypeResponseBuilders\StandardReadingTypeResponseBuilder;
use App\Sensors\Entity\ReadingTypes\BoolReadingTypes\BoolReadingSensorInterface;
use App\Sensors\Entity\ReadingTypes\StandardReadingTypes\StandardReadingSensorInterface;
use App\Sensors\Entity\SensorTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Exceptions\ReadingTypeNotSupportedException;

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
