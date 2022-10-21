<?php

namespace App\Sensors\Command\Elasticsearch\IndicesCreation;

use Elastica\Exception\InvalidException;
use Elastica\Exception\ResponseException;
use Elastica\Index;
use Elastica\Mapping;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ElasticCreateOutOfBoundsIndices extends Command
{
    protected static $defaultName = 'app:elastic-create-out-of-bounds-indices';

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
        $this
            ->setDescription('Creates the indices for the out of bounds sensor types in ElasticSearch')
            ->setHelp('This command creates the indices for the out of bounds sensor types in ElasticSearch');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'ElasticSearch Out of Bounds Indices',
            '======================================',
        ]);

        $output->writeln('Creating indices...');

        foreach ($this->indexMappings as $mappingProperties) {
            /** @var Index $index */
            $index = $mappingProperties['index'];
            $output->writeln("Creating index: " . $index->getName());

            if ($index->exists()) {
                $output->writeln('<info>Index already exists: ' . $index->getName() . '</info>');
                continue;
            }


            try {
                $index->create([], ['recreate' => true]);
            } catch (InvalidException|ResponseException $e) {
                $output->writeln('<error>' . $e->getMessage() . '</error>');
                continue;
            }

            $mappings = $mappingProperties['mapping'];
            $mapping = new Mapping();
            $mapping->setProperties([
                'outOfRangeID' => ['type' => 'integer'],
//                $mappings['sensorFieldName'] => ['type' => 'integer'],
                'sensorReadingID' => ['type' => 'integer'],
                'sensorReading' => ['type' => $mappings['sensorReading']],
                'createdAt' => ['type' => 'date'],
            ]);

            $mapping->send($index);
            $output->writeln('<info>Index created: ' . $index->getName() . '</info>');
        }

        $output->writeln('<info>Indices created successfully!</info>');

        return Command::SUCCESS;
    }
}
