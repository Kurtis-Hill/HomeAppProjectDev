<?php
declare(strict_types=1);

namespace App\Common\Command;

use Elastica\Index;
use Elastica\Mapping;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:elastic-create-log-index',
    description: 'Creates the indices for the constant record types in ElasticSearch.',
    aliases: ['app:elastic-create-log-index'],
    hidden: false
)]
class ElasticLogIndexCommand extends Command
{
    #[ArrayShape([
        'index' => Index::class,
        'mapping' => [
            'sensorFieldName' => 'humidityID',
            'sensorReading' => 'float',
        ],
    ])]
    private array $indexMappings;

    public function __construct(string $name = null, array $indexMappings = [])
    {
        $this->indexMappings = $indexMappings;

        parent::__construct($name);
    }

    protected function configure(): void
    {
        $this->setDescription('Creates the index for ElasticSearch application logs');
        $this->addArgument('force', InputArgument::OPTIONAL, 'Force recreation of indices');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'ElasticSearch Application Log Index',
            '======================================',
            'Creating index...'
        ]);

        $force = $input->getArgument('force') === 'f' || $input->getArgument('force') === 'y';
        foreach ($this->indexMappings as $mappingProperties) {
            /** @var Index $index */
            $index = $mappingProperties['index'];
            $output->writeln("Creating index: " . $index->getName());

            if ($force === false && $index->exists()) {
                $output->writeln('<info>Index already exists: ' . $index->getName() . '</info>');
            } else {
                $index->create([], ['recreate' => true]);

                $mapping = new Mapping();
                $mappings = $mappingProperties['mapping'];
                $mapping->setProperties($mappings);
                $mapping->send($index);

                $output->writeln('<info>Index created: ' . $index->getName() . '</info>');
            }
        }
        $output->writeln('<info>Index Created</info>');

        return Command::SUCCESS;
    }
}
