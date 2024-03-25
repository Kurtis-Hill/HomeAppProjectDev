<?php

namespace App\Sensors\Command;

use App\Sensors\Repository\SensorTriggerRepository;
use App\Sensors\SensorServices\Trigger\TriggerHelpers\TriggerDateTimeConvertor;
use DateTimeImmutable;
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
        private readonly SensorTriggerRepository $sensorTriggerRepository,
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

        $currentTime = TriggerDateTimeConvertor::prepareTimesForComparison();
        $currentDay = TriggerDateTimeConvertor::prepareDaysForComparison();

        $output->writeln(sprintf('Current time: %s', $now->format('d-m-Y H:i:s')));

        $triggers = $this->sensorTriggerRepository->findAllSensorTriggersForDayAndTime(
            $currentDay,
            $currentTime,
        );

        $output->writeln(sprintf('%d Triggers found.', count($triggers)));




        return Command::SUCCESS;
    }
}
