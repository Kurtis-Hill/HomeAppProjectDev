<?php
declare(strict_types=1);

namespace App\Command;

use App\Services\Common\Client\HomeAppAlertClientInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:slack-alert-checker',
    description: 'Check if there are any alerts in the system and send them to slack',
    aliases: ['app:slack-alert-checker'],
    hidden: false
)]
class SlackAltertCheckerCommand extends Command
{
    public function __construct(
        private readonly HomeAppAlertClientInterface $homeAppAlertClient,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Check if there are any alerts in the system and send them to slack');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->homeAppAlertClient->sendAlert('This is a test message');

        return Command::SUCCESS;
    }
}
