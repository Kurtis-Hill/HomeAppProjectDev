<?php

namespace App\Sensors\Factories\ReadingTypeFactories;

use App\Sensors\Builders\ReadingTypeResponseBuilders\ReadingTypeResponseBuilderInterface;
use App\Sensors\Builders\ReadingTypeResponseBuilders\StandardReadingTypeResponseBuilder;
use App\Sensors\Entity\ReadingTypes\Interfaces\AllSensorReadingTypeInterface;
use App\Sensors\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;
use App\Sensors\Exceptions\ReadingTypeNotSupportedException;

class ReadingTypeResponseBuilderFactory
{
    private StandardReadingTypeResponseBuilder $standardReadingTypeResponseBuilder;

    public function __construct(StandardReadingTypeResponseBuilder $standardReadingTypeResponseBuilder)
    {
        $this->standardReadingTypeResponseBuilder = $standardReadingTypeResponseBuilder;
    }

    /**
     * @throws ReadingTypeNotSupportedException
     */
    public function getStandardReadingTypeResponseBuilder(AllSensorReadingTypeInterface $readingType): ReadingTypeResponseBuilderInterface
    {
        if ($readingType instanceof StandardReadingSensorInterface) {
            return $this->standardReadingTypeResponseBuilder;
        }

        throw new ReadingTypeNotSupportedException(
            sprintf(
                ReadingTypeNotSupportedException::READING_TYPE_NOT_SUPPORTED_FOR_THIS_SENSOR_MESSAGE,
                $readingType->getReadingType(),
            )
        );
    }
}
