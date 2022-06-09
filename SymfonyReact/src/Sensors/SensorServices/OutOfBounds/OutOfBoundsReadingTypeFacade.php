<?php

namespace App\Sensors\SensorServices\OutOfBounds;

use App\Sensors\Entity\ReadingTypes\Interfaces\StandardReadingSensorInterface;
use App\Sensors\Factories\ORMFactories\OufOfBounds\OutOfBoundsORMFactory;
use App\Sensors\Factories\ReadingTypeFactories\OutOfBoundsCreationFactory;

class OutOfBoundsReadingTypeFacade implements SensorOutOfBoundsHandlerInterface
{
    private OutOfBoundsCreationFactory $outOfBoundsCreationFactory;

    private OutOfBoundsORMFactory $outOfBoundsORMFactory;

    public function __construct(
        OutOfBoundsCreationFactory $outOfBoundsCreationFactory,
        OutOfBoundsORMFactory $outOfBoundsORMFactory,
    ) {
        $this->outOfBoundsORMFactory = $outOfBoundsORMFactory;
        $this->outOfBoundsCreationFactory = $outOfBoundsCreationFactory;
    }

    public function processOutOfBounds(StandardReadingSensorInterface $readingTypeObject): void
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
