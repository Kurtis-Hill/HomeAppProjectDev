<?php

namespace App\Sensors\SensorDataServices\OutOfBounds;

use App\Sensors\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;
use App\Sensors\Factories\ORMFactories\OufOfBounds\OutOfBoundsORMFactoryInterface;
use App\Sensors\Factories\ReadingTypeFactories\OutOfBoundsCreationFactory;

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

    public function checkAndProcessOutOfBounds(StandardReadingSensorInterface $readingTypeObject): void
    {
        if ($readingTypeObject->isReadingOutOfBounds()) {
            $readingType = $readingTypeObject->getReadingType();
            $outOfBoundsObjectBuilder = $this->outOfBoundsCreationFactory->getConstRecordObjectBuilder($readingType);

            $outOfBoundsObject = $outOfBoundsObjectBuilder->buildOutOfBoundsObject($readingTypeObject);

            $outOfBoundsRepository = $this->outOfBoundsORMFactory->getOutOfBoundsServiceRepository($readingType);
            $outOfBoundsRepository->persist($outOfBoundsObject);
        }
    }

}
