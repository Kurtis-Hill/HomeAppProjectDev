<?php

namespace App\Sensors\Command;

use Symfony\Component\Console\Command\Command;
//use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

//#[AsCommand(
//    name: 'app:create-user',
//    description: 'Creates a new user.',
//    hidden: false,
//    aliases: ['app:add-user']
//)]
class UpdateSensorCommand extends Command
{
    protected static $defaultName = 'esp:update-update-current-reading';

    protected function configure(): void
    {
        $this
            ->setName(self::$defaultName)
            ->setDescription('This command allows you to update current reading of sensor.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            '<info>',
            'Updating Current Readings',
            '======================',
            '</info>',
        ]);

        $output->writeln('<info>Updating current reading of sensor...</info>');

        $output->writeln('<info>Current reading of sensor updated successfully.</info>');

        return Command::SUCCESS;
    }
}
