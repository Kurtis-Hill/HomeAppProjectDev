<?php

namespace App\Scheduler\Task;
use App\Repository\Sensor\SensorReadingType\ORM\BoolReadingBaseSensorRepository;
use App\Repository\Sensor\SensorReadingType\ORM\StandardReadingTypeRepository;
use App\Services\Sensor\Trigger\SensorTriggerProcessor\ReadingTriggerHandler;
use DateTimeImmutable;
use Psr\Log\LoggerInterface;
use Symfony\Component\Scheduler\Attribute\AsCronTask;

#[AsCronTask('* * * * *')]
class CheckForTriggersTask
{
    public function __construct(
        private readonly ReadingTriggerHandler $readingTriggerHandler,
        private readonly StandardReadingTypeRepository $standardReadingTypeRepository,
        private readonly BoolReadingBaseSensorRepository $boolReadingBaseSensorRepository,
        private readonly LoggerInterface $elasticLogger,
    ) {
    }

    public function __invoke(): void
    {
        $now = new DateTimeImmutable();

        $this->elasticLogger->info(sprintf('Trigger check started at %s', $now->format('d-m-Y H:i:s')));

        $allStandardSensors = $this->standardReadingTypeRepository->findAll();
        $allBoolSensors = $this->boolReadingBaseSensorRepository->findAll();
        $allSensors = array_merge($allStandardSensors, $allBoolSensors);

        foreach ($allSensors as $sensor) {
            $this->readingTriggerHandler->handleTrigger(
                $sensor,
            );
        }
    }

    public function getName(): string
    {
        return 'check_for_triggers_task';
    }
}
