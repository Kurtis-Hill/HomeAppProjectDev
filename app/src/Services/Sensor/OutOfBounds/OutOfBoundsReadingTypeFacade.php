<?php

namespace App\Services\Sensor\OutOfBounds;

use App\Entity\Sensor\ReadingTypes\StandardReadingTypes\StandardReadingSensorInterface;
use App\Factories\Sensor\OufOfBounds\OutOfBoundsFactoryInterface;
use App\Factories\Sensor\ReadingTypeFactories\OutOfBoundsEntityCreationFactory;
use Throwable;

readonly class OutOfBoundsReadingTypeFacade implements SensorOutOfBoundsHandlerInterface
{
    public function __construct(
        private OutOfBoundsEntityCreationFactory $outOfBoundsCreationFactory,
        private OutOfBoundsFactoryInterface $outOfBoundsFactory,
        private OutOfBoundsAlertHandler $outOfBoundsAlertHandler,
    ) {
    }

    /**
     * @throws Throwable
     */
    public function processOutOfBounds(StandardReadingSensorInterface $readingTypeObject): void
    {
        if ($readingTypeObject->isReadingOutOfBounds()) {
            $readingType = $readingTypeObject->getReadingType();
            $outOfBoundsObjectBuilder = $this->outOfBoundsCreationFactory->getConstRecordObjectBuilder($readingType);

            $outOfBoundsObject = $outOfBoundsObjectBuilder->buildOutOfBoundsObject($readingTypeObject);

            $exception = null;
            try {
                $outOfBoundsRepository = $this->outOfBoundsFactory->getOutOfBoundsServiceRepository($readingType);
                $outOfBoundsRepository->persist($outOfBoundsObject);
            } catch (Throwable $e) {
                $exception = $e;
            }

            try {
                $this->outOfBoundsAlertHandler->handlerAlert(
                    $readingTypeObject,
                    $outOfBoundsObject,
                );
            } catch (Throwable $e) {
                $exception = $e;
            }

            if ($exception !== null) {
                throw $exception;
            }
        }
    }
}
