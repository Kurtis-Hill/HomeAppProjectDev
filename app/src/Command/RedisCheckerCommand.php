<?php

namespace App\Command;

use Exception;
use Predis\Client;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:redis-checker',
    description: 'Check if redis is up and running',
    aliases: ['app:redis-checker'],
    hidden: false
)]
class RedisCheckerCommand extends Command
{
    public function __construct(
        private Client $redisClient,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Check if redis is up and running');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'Redis Checker',
            '======================================',
            'Checking redis...'
        ]);

        try {
            $response = $this->redisClient->ping();

            if ($response) {
                $output->writeln('<info>Redis is up and running</info>');
                $keys = $this->redisClient->keys('*');
                $output->writeln('<info>Redis keys:</info>');
                foreach ($keys as $key) {
                    $output->writeln($key);
                }
                return Command::SUCCESS;
            }
        } catch (Exception $e) {
            $output->writeln(sprintf('<error>Redis is not running exception %s</error>', $e->getMessage()));
        }

        $output->writeln('<error>Redis is not running</error>');

        return Command::FAILURE;
    }
}
