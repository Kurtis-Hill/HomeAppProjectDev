<?php

namespace App\Command\Sensor;

use App\Repository\Sensor\SensorReadingType\ORM\BoolReadingBaseSensorRepository;
use App\Repository\Sensor\SensorReadingType\ORM\StandardReadingTypeRepository;
use App\Services\Sensor\Trigger\SensorTriggerProcessor\ReadingTriggerHandler;
use DateTimeImmutable;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:trigger-check',
    description: 'Check for any triggers to be activated.',
    aliases: ['trigger:check'],
    hidden: false
)]
class TriggerCheckCommand extends Command
{
    public function __construct(
        private readonly ReadingTriggerHandler $readingTriggerHandler,
        private readonly StandardReadingTypeRepository $standardReadingTypeRepository,
        private readonly BoolReadingBaseSensorRepository $boolReadingBaseSensorRepository,
        private readonly LoggerInterface $elasticLogger,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Check for any triggers to be activated.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'Checking for triggers...',
            '======================================',
        ]);

        $now = new DateTimeImmutable();

        $output->writeln(sprintf('Current time: %s', $now->format('d-m-Y H:i:s')));
        $this->elasticLogger->info(sprintf('Trigger check started at %s', $now->format('d-m-Y H:i:s')));

        $allStandardSensors = $this->standardReadingTypeRepository->findAll();
        $allBoolSensors = $this->boolReadingBaseSensorRepository->findAll();
        $allSensors = array_merge($allStandardSensors, $allBoolSensors);

        foreach ($allSensors as $sensor) {
            $output->writeln(sprintf('Checking base reading type id: %d', $sensor->getBaseReadingType()->getBaseReadingTypeID()));
            $this->readingTriggerHandler->handleTrigger(
                $sensor,
            );
        }

        $output->writeln('Triggers checked.');

        return Command::SUCCESS;
    }
}
