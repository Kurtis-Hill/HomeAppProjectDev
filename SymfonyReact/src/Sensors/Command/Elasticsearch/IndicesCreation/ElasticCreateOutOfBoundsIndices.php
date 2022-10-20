<?php

namespace App\Sensors\Command\Elasticsearch\IndicesCreation;

use Elastica\Exception\InvalidException;
use Elastica\Exception\ResponseException;
use Elastica\Mapping;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ElasticCreateOutOfBoundsIndices extends AbstractIndieCreationCommand
{
    protected static $defaultName = 'app:elastic-create-out-of-bounds-indices';

    private const MAPPING_PROPERTY_INDEX = 'outofbounds_';

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
            '',
        ]);

        $output->writeln('Creating indices...');

        foreach (self::INDICES_TO_CREATE as $indexName => $mappingProperties) {
            $indexNameConcat = sprintf('%s%s', self::MAPPING_PROPERTY_INDEX, $indexName);
            $output->writeln('Creating index: ' . $indexNameConcat);
            $index = $this->elasticSearchClient->getIndex($indexNameConcat);

            try {
                $index->create([], ['recreate' => false]);
            } catch (InvalidException|ResponseException $e) {
                $output->writeln('<error>' . $e->getMessage() . '</error>');
                continue;
            }

            $mapping = new Mapping();
            $mapping->setProperties([
                'constRecordID' => ['type' => 'integer'],
                $mappingProperties['sensorFieldName'] => ['type' => 'integer'],
                'sensorReading' => ['type' => $mappingProperties['sensorReading']],
                'createdAt' => ['type' => 'date'],
            ]);

            $mapping->send($index);
            $output->writeln('<info>Index created: ' . $indexNameConcat . '</info>');
        }

        $output->writeln('<info>Indices created successfully!</info>');

        return Command::SUCCESS;
    }
}
