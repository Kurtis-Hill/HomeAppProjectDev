<?php

namespace App\ESPDeviceSensor\SensorDataServices\OutOfBounds;

use App\ESPDeviceSensor\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;
use App\ESPDeviceSensor\Factories\ORMFactories\OufOfBounds\OutOfBoundsORMFactoryInterface;
use App\ESPDeviceSensor\Factories\ReadingTypeFactories\OutOfBoundsCreationFactory;

class SensorReadingTypeOutOfBoundsService implements OutOfBoundsSensorServiceInterface
{
    private OutOfBoundsCreationFactory $outOfBoundsCreationFactory;

    private OutOfBoundsORMFactoryInterface $outOfBoundsORMFactory;

    public function __construct(
        OutOfBoundsCreationFactory $outOfBoundsCreationFactory,
        OutOfBoundsORMFactoryInterface $outOfBoundsORMFactory,
    ) {
        $this->outOfBoundsORMFactory = $outOfBoundsORMFactory;
        $this->outOfBoundsCreationFactory = $outOfBoundsCreationFactory;
    }

    public function checkAndHandleSensorReadingOutOfBounds(StandardReadingSensorInterface $readingTypeObject): void
    {
//        dd($readingTypeObject);
        if ($readingTypeObject->isReadingOutOfBounds()) {
            $readingType = $readingTypeObject->getReadingType();
            $outOfBoundsObjectBuilder = $this->outOfBoundsCreationFactory->getConstRecordObjectBuilder($readingType);

            $outOfBoundsObject = $outOfBoundsObjectBuilder->buildOutOfBoundsObject($readingTypeObject);

            $outOfBoundsRepository = $this->outOfBoundsORMFactory->getOutOfBoundsServiceRepository($readingType);
            $outOfBoundsRepository->persist($outOfBoundsObject);
        }
    }

}
