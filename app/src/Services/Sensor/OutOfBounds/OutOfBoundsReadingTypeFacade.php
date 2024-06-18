<?php

namespace App\Services\Sensor\OutOfBounds;

use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\StandardReadingSensorInterface;
use App\Factories\Sensor\OufOfBounds\OutOfBoundsFactoryInterface;
use App\Factories\Sensor\ReadingTypeFactories\OutOfBoundsEntityCreationFactory;

class OutOfBoundsReadingTypeFacade implements SensorOutOfBoundsHandlerInterface
{
    private OutOfBoundsEntityCreationFactory $outOfBoundsCreationFactory;

    private OutOfBoundsFactoryInterface $outOfBoundsFactory;

    public function __construct(
        OutOfBoundsEntityCreationFactory $outOfBoundsCreationFactory,
        OutOfBoundsFactoryInterface $outOfBoundsFactory,
    ) {
        $this->outOfBoundsFactory = $outOfBoundsFactory;
        $this->outOfBoundsCreationFactory = $outOfBoundsCreationFactory;
    }

    public function processOutOfBounds(StandardReadingSensorInterface $readingTypeObject): void
    {
        if ($readingTypeObject->isReadingOutOfBounds()) {
            $readingType = $readingTypeObject->getReadingType();
            $outOfBoundsObjectBuilder = $this->outOfBoundsCreationFactory->getConstRecordObjectBuilder($readingType);

            $outOfBoundsObject = $outOfBoundsObjectBuilder->buildOutOfBoundsObject($readingTypeObject);

            $outOfBoundsRepository = $this->outOfBoundsFactory->getOutOfBoundsServiceRepository($readingType);
            $outOfBoundsRepository->persist($outOfBoundsObject);
        }
    }
}
