<?php

namespace App\Sensors\SensorServices\OutOfBounds;

use App\Sensors\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;
use App\Sensors\Factories\OufOfBounds\OutOfBoundsElasticFactory;
use App\Sensors\Factories\OufOfBounds\OutOfBoundsFactoryInterface;
use App\Sensors\Factories\OufOfBounds\OutOfBoundsORMFactory;
use App\Sensors\Factories\ReadingTypeFactories\OutOfBoundsEntityCreationFactory;

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
