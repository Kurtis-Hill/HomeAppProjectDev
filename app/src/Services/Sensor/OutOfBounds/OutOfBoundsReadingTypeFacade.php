<?php

namespace App\Services\Sensor\OutOfBounds;

use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\StandardReadingSensorInterface;
use App\Factories\Sensor\OufOfBounds\OutOfBoundsFactoryInterface;
use App\Factories\Sensor\ReadingTypeFactories\OutOfBoundsEntityCreationFactory;

readonly class OutOfBoundsReadingTypeFacade implements SensorOutOfBoundsHandlerInterface
{
    public function __construct(
        private OutOfBoundsEntityCreationFactory $outOfBoundsCreationFactory,
        private OutOfBoundsFactoryInterface $outOfBoundsFactory,
        private OutOfBoundsAlertHandler $outOfBoundsAlertHandler,
    ) {
    }

    public function processOutOfBounds(StandardReadingSensorInterface $readingTypeObject): void
    {
        if ($readingTypeObject->isReadingOutOfBounds()) {
            $readingType = $readingTypeObject->getReadingType();
            $outOfBoundsObjectBuilder = $this->outOfBoundsCreationFactory->getConstRecordObjectBuilder($readingType);

            $outOfBoundsObject = $outOfBoundsObjectBuilder->buildOutOfBoundsObject($readingTypeObject);

            $outOfBoundsRepository = $this->outOfBoundsFactory->getOutOfBoundsServiceRepository($readingType);
            $outOfBoundsRepository->persist($outOfBoundsObject);

            $this->outOfBoundsAlertHandler->handlerAlert(
                $readingTypeObject,
                $outOfBoundsObject,
            );
        }
    }
}
